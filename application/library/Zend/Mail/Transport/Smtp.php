<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Mail_Transport_Abstract
 */
require_once 'Zend/Mail/Transport/Abstract.php';


/**
 * SMTP connection object
 * minimum implementation according to RFC2821:
 * EHLO, MAIL FROM, RCPT TO, DATA, RSET, NOOP, QUIT
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Mail_Transport_Smtp extends Zend_Mail_Transport_Abstract {

    const CONNECTION_TIMEOUT = 30;
    const COMMUNICATION_TIMEOUT = 2;
    const DEBUG = false;

    protected $_host;
    protected $_port;
    protected $_myName;

    /**
     * Last Response from the SMTP server, 1 Array Element per line
     *
     * @var array of strings
     */
    public $lastResponse = array();

    /**
     * Stream to SMTP Server
     *
     * @var Stream
     */
    protected $_con = null;

    /**
     * Constructor.
     *
     * @param string $host
     * @param int $port
     * @param string $myName  (for use with HELO)
     */
    public function __construct($host = '127.0.0.1',
                                $port = null,
                                $myName = '127.0.0.1')
    {
        if ($port === null) {
            if (($port = ini_get('smtp_port')) == '') {
                $port = 25;
            }
        }

        $this->_host = $host;
        $this->_port = $port;
        $this->_myName = $myName;
    }


    /**
     * Connect to the server with the parameters given
     * in the constructor and send "HELO". The connection
     * is immediately closed if an error occurs.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function connect()
    {
        $errno  = null;
        $errstr = null;

        // open connection
        $fp = stream_socket_client('tcp://' . $this->_host .':'.$this->_port, $errno, $errstr, self::CONNECTION_TIMEOUT);

        if ($fp===false) {
            if ($errno==0) {
                $msg = 'Could not open socket';
            } else {
                $msg = $errstr;
            }
            throw new Zend_Mail_Transport_Exception($msg);
        }

        $this->_con = $fp;

        try {
            $res = stream_set_timeout($this->_con, self::COMMUNICATION_TIMEOUT );
            if ($res === false) {
                throw new Zend_Mail_Transport_Exception('Could not set Stream Timeout');
            }

            /**
             * Now the connection is open. Wait for the welcome message:
             *   welcome message has error code 220
             */
            $this->_expect(220);
            $this->helo($this->_myName);
        } catch (Zend_Mail_Transport_Exception $e) {
            fclose($fp);
            throw $e;
        }
    }


    /**
     * Sends EHLO along with the given machine name and
     * validates server response. If EHLO fails, HELO is
     * sent for compatibility with older MTAs.
     *
     * @param string $myname
     * @throws Zend_Mail_Transport_Exception
     */
    public function helo($myname)
    {
        $this->_send('EHLO '.$myname);

        try {
            $this->_expect(250);  // Hello OK is code 250
        } catch (Zend_Mail_Transport_Exception $e) {
            // propably wrong status code, RFC 2821 requires sending HELO in this case:
            $this->_send('HELO '.$myname);
            $this->_expect(250); // if we get an exception here, we give up...
        }
    }


    /**
     * sends a MAIL command for the senders address
     * and validates the response.
     *
     * @param string $from_email
     * @throws Zend_Mail_Transport_Exception
     */
    public function mail_from($from_email)
    {
        $this->_send('MAIL FROM:<' . $from_email . '>');
        $this->_expect(250);
    }

    /**
     * sends a RCPT command for a recipient address
     * and validates the response.
     *
     * @param string $to
     * @throws Zend_Mail_Transport_Exception
     */
    public function rcpt_to($to)
    {
        $this->_send('RCPT TO:<' . $to . '>');
        $this->_expect(250,251);
    }

    /**
     * sends the DATA command followed by the
     * email content (headers plus body) folowed
     * by a dot and validates the response of the
     * server.
     *
     * @param string $data
     * @throws Zend_Mail_Transport_Exception
     */
    public function data($data)
    {
        $this->_send('DATA');
        $this->_expect(354);
        foreach(explode($this->EOL, $data) as $line) {
            if (strpos($line, '.') === 0) {
                // Escape lines prefixed with a '.'
                $line = '.' . $line;
            }
            $this->_send($line);
        }
        $this->_send('.');
        $this->_expect(250);
    }


    /**
     * Sends the RSET command end validates answer
     * Not used by Zend_Mail, can be used to restore a clean
     * smtp communication state when a transaction has
     * been cancelled.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function rset()
    {
        $this->_send('RSET');
        $this->_expect(250);
    }


    /**
     * Sends the NOOP command end validates answer
     * Not used by Zend_Mail, could be used to keep a connection
     * alive or check if it is still open.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function noop()
    {
        $this->_send('NOOP');
        $this->_expect(250);
    }


    /**
     * Sends the VRFY command end validates answer
     * The calling method needs to evaluate $this->lastResponse
     * This function was implemented for completeness only.
     * It is not used by Zend_Mail.
     *
     * @param string $user User Name or eMail to verify
     * @throws Zend_Mail_Transport_Exception
     */
    public function vrfy($user)
    {
        $this->_send('VRFY ' . $user);
        $this->_expect(250,251,252);
    }


    /**
     * Sends the QUIT command and validates answer
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function quit()
    {
        $this->_send('QUIT');
        $this->_expect(221);
    }


    /**
     * close an existing connection.
     * sends QUIT and closes stream.
     *
     * @throws Zend_Mail_Transport_Exception
     */
    public function disconnect()
    {
        $this->quit();
        fclose($this->_con);
        $this->_con = NULL;
    }


    /**
     * Read the response from the stream and
     * check for expected return code. throws
     * a Zend_Mail_Transport_Exception if an unexpected code
     * is returned
     *
     * @param int $val1
     * @param int $val2
     * @param int $val3
     * @throws Zend_Mail_Transport_Exception
     */
    protected function _expect($val1, $val2=null, $val3=null)
    {
        /**
         * according to the new RFC2821, a multiline response can be sent
         * so we now check if it is the case here.
         * a multiline response is structured as follows:
         *   250-ok welcome 127.0.0.1
         *   250-PIPELINING
         *   250 HELP
         * normal answer would be:
         *
         * 250 ok.
         */
        $this->lastResponse = array();

        do {
            // blocking
            $res = $this->_receive();

            // we might need this later
            $this->lastResponse[] = $res;

            // returncode is always 3 digits at the beginning of the line
            $errorcode = substr($res,0,3);
            if ($errorcode === NULL || ( ($errorcode!=$val1) && ($errorcode!=$val2) && ($errorcode!=$val3)) ) {
                throw new Zend_Mail_Transport_Exception($res);
            }
        } while($res[3]=='-');
    }


    /**
     * Get a line from the stream. includes error checking and debugging
     *
     * @return string
     * @throws Zend_Mail_Transport_Exception
     */
    protected function _receive()
    {
        $res = fgets($this->_con, 1024);

        if ($res === false) {
            throw new Zend_Mail_Transport_Exception('Could not read from SMTP server');
        }

        if (self::DEBUG) {
            echo "R: $res<br>\n";
        }

        return $res;
    }


    /**
     * Send the given string followed by a LINEEND to the server
     *
     * @param string $str
     * @throws Zend_Mail_Transport_Exception
     */
    protected function _send($str)
    {
        $res = fwrite($this->_con, $str . $this->EOL);
        if ($res === false) {
            throw new Zend_Mail_Transport_Exception('Could not write to SMTP server');
        }

        if (self::DEBUG) {
            echo "S: $str<br>\n";
        }
    }

    /**
     * Send an email
     *
     * @param array $to
     */
    public function _sendMail()
    {
        // Check if connection already present
        $wasConnected = ($this->_con !== null);
        if (!$wasConnected) {
            // establish a connection
            $this->connect();
        } else {
            // reset conection
            $this->rset();
        }

        try {
            $this->mail_from($this->_mail->getReturnPath());
            foreach ($this->_mail->getRecipients() as $recipient) {
                $this->rcpt_to($recipient);
            }
            $this->data($this->header . $this->EOL . $this->body);
        } catch (Zend_Mail_Transport_Exception $e) {
            // remove connection if we made one
            if (!$wasConnected) {
                $this->disconnect();
            }

            // rethrow
            throw $e;
        }

        // remove connection if we made one
        if(!$wasConnected) {
            $this->disconnect();
        }
    }
}
