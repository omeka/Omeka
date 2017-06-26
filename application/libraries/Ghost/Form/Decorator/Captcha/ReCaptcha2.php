<?php

class Ghost_Form_Decorator_Captcha_ReCaptcha2 extends Zend_Form_Decorator_Abstract
{
    /**
     * Render captcha
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element_Captcha) {
            return $content;
        }

        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $placement     = $this->getPlacement();
        $separator     = $this->getSeparator();
        $captcha       = $element->getCaptcha();
        $markup        = $captcha->render($view, $element);

        switch ($placement) {
            case 'PREPEND':
                $content = $markup . $separator . $content;
                break;
            case 'APPEND':
            default:
                $content = $content . $separator . $markup;
        }
        return $content;
    }
}

