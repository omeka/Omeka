<?php
//error_reporting(E_ALL ^ E_NOTICE);
//ini_set("display_errors", 'on');

add_plugin_hook('install', 'email_notification_install');
add_plugin_hook('uninstall', 'email_notification_uninstall');
add_plugin_hook('config', 'email_notification_config');
add_plugin_hook('config_form', 'email_notification_config_form');

add_plugin_hook('after_insert_item', 'email_notification_after_insert_item');

function email_notification_install()
{
    set_option('email_notification_send_public_notification', '0');
    set_option('email_notification_public_notification_email', '');
    set_option('email_notification_send_user_notification', '0');
    set_option('email_notification_public_notification_email_from', '');
}

function email_notification_uninstall()
{
    delete_option('email_notification_send_public_notification');
    delete_option('email_notification_public_notification_email');
    delete_option('email_notification_public_notification_email_from');
    delete_option('email_notification_send_user_notification');
    delete_option('email_notification_site_acct');
}

function email_notification_config()
{
    set_option('email_notification_site_acct', $_POST['email_notification_site_acct']);
    set_option('email_notification_send_public_notification', $_POST['send_public_notification']);
    set_option('email_notification_public_notification_email', $_POST['public_notification_email']);
    set_option('email_notification_public_notification_email_from', $_POST['public_notification_email_from']);
    set_option('email_notification_send_user_notification', $_POST['send_user_notification']);
}

function email_notification_config_form()
{ 
    $siteAccountId = get_option('email_notification_site_acct'); 
?>
    <div class="field">

        <label for="email_notification_send_public_notification">Send to email address:</label>
        <?php echo __v()->formCheckbox('send_public_notification', true, 
        array('checked'=>(boolean)get_option('email_notification_send_public_notification'))); ?>
        <p class="explanation">If checked, email notification will be sent to specified address if item is added.</p>

        <label for="email_notification_public_notification_email">Email address (To):</label>
        <?php echo __v()->formText('public_notification_email', get_option('email_notification_public_notification_email'), array('size'=>35)); ?>
        <p class="explanation">Email notification will be sent to this email address.</p>

        <label for="email_notification_public_notification_email_from">Email address (From):</label>
        <?php echo __v()->formText('public_notification_email_from', get_option('email_notification_public_notification_email_from'), array('size'=>35)); ?>
        <p class="explanation">Email notification will be sent from this email address.</p>

        <label for="email_notification_send_user_notification">Send to registered users:</label>
        <?php echo __v()->formCheckbox('send_user_notification', true, 
        array('checked'=>(boolean)get_option('email_notification_send_user_notification'))); ?>
        <p class="explanation">Email notification will be sent to registered users.</p>

    </div>
<?php
}

function email_notification_after_insert_item(Item $item)
{
    require_once '../application/helpers/ItemFunctions.php';
    require_once '../application/helpers/Functions.php';
    require_once '../application/helpers/StringFunctions.php';
    require_once '../application/helpers/UserFunctions.php';
    /**
     * @var User
     */
    $creator = $item->getUserWhoCreated()->getEntity();
    $title = $item->Elements[50][0]['text'];
//    $v = show_item_metadata(array('return_type' => 'array', 'show_empty_elements'=>true), get_item_by_id($item->id));
//    var_dump($v);exit;
//    var_dump(item('Dublin Core', 'Title', array(), $item->getTable('Item')->find($item->id))); //$item->getTable('Item')->find($item->id)->toArray()
//    exit;
    $email_notification_send_public_notification = (boolean)get_option('email_notification_send_public_notification');
    if ($email_notification_send_public_notification) 
    {
        $body = "
Hello Everybody,
    
".$creator->first_name.' '.$creator->last_name." added new '".get_option('site_title')."' item of type '".$item->getItemType()->name."': 
$title



Please do not reply to this email.

Thanks,
".get_option('site_title')." Administration
";
        echo send_notification(get_option('email_notification_public_notification_email'), $body, $title);
    }
    $email_notification_send_user_notification = (boolean)get_option('email_notification_send_user_notification');
    if ($email_notification_send_user_notification)
    {
        $users = get_users(array(), 10000);
        foreach ($users as $user)
        {
            $creator_name = ($user->id == $creator->id)?"You":$creator->first_name." ".$creator->last_name;
            $body = "
Hello ".$user->first_name." ".$user->last_name.",
    
$creator_name added new '".get_option('site_title')."' item of type '".$item->getItemType()->name."': 
$title



Please do not reply to this email.

Thanks,
".get_option('site_title')." Administration
";
            echo send_notification($user->email, $body, $title);
        }
        exit;
    }
}


function send_notification($addTo, $body, $title)
{
    $mail = new Zend_Mail('UTF-8');
    $mail->addTo($addTo);                
    $mail->addHeader('X-Mailer', 'PHP/' . phpversion());
    $mail->setBodyText($body);
    $mail->setFrom(get_option('email_notification_public_notification_email_from'), get_option('site_title')." Administrator");
    $mail->setSubject(get_option('site_title')." new item: $title");
    $mail->send();
}

