<?php
/**
 * 2025 Corporation for Digital Scholarship
 */

/**
 * Custom decorator for default labels
 * 
 * @package Omeka\Form\Decorator
 */
class Omeka_Form_Decorator_RawAffixLabel extends Zend_Form_Decorator_Label
{
    /**
     * Get label to render
     *
     * @return string
     */
    public function getLabel()
    {
        if (null === ($element = $this->getElement())) {
            return '';
        }

        $label = (string) $element->getLabel();
        $label = trim($label);

        if (empty($label)) {
            return '';
        }

        return $label;
    }

    /**
     * Retrieve element ID (used in 'for' attribute)
     *
     * If none set in decorator, looks first for element 'id' attribute, and
     * defaults to element name.
     *
     * (Overriding the parent's implementation to remove a setId call that
     * improperly causes the decorator to remember the first element's ID.)
     *
     * @return string
     */
    public function getId()
    {
        $id = $this->getOption('id');
        if (null === $id) {
            if (null !== ($element = $this->getElement())) {
                $id = $element->getId();
            }
        }

        return $id;
    }

    /**
     * Return label with required suffix or prefix.
     * Includes custom default suffix.
     *
     * @return string
     */
    public function handleRequired($label, $element) {
        $optPrefix = $this->getOptPrefix();
        $optSuffix = $this->getOptSuffix();
        $reqPrefix = $this->getReqPrefix();
        $reqSuffix = $this->getReqSuffix();

        if (!empty($label)) {
            if ($element->isRequired()) {
                $label = $reqPrefix . $label . $reqSuffix;
            } else {
                $label = $optPrefix . $label . $optSuffix;
            }
        }

        return $label;
    }

    /**
     * Render a label
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $label     = $this->getLabel();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $tagClass  = $this->getTagClass();
        $id        = $this->getId();
        $class     = $this->getClass();
        $options   = $this->getOptions();

        if (empty($label) && empty($tag)) {
            return $content;
        }

        if (!empty($label)) {
            $label = trim($label);

            $formLabelOptions = [];
            $formLabelOptions['id'] = $id;
            $formLabelOptions['class'] = $class;

            // Give formLabel already escaped label content alongside
            // non-escaped required markup.
            $formLabelOptions['escape'] = false;

            if ($element instanceof Zend_Form_Element_Radio || $element instanceof Zend_Form_Element_MultiCheckbox) {
                $formLabelOptions['disableFor'] = true;
            }

            // Escape the label, but not the required markup.
            if (!isset($options['escape']) || ($options['escape'] == true)){
                $label = $view->escape($label);
            }
            $label = $this->handleRequired($label, $element);

            switch ($placement) {
                case self::IMPLICIT:
                    // Break was intentionally omitted

                case self::IMPLICIT_PREPEND:
                    $formLabelOptions['disableFor'] = true;

                    $label = $view->formLabel(
                        $element->getFullyQualifiedName(),
                        $label . $separator . $content,
                        $formLabelOptions
                    );
                    break;

                case self::IMPLICIT_APPEND:
                    $formLabelOptions['disableFor'] = true;

                    $label = $view->formLabel(
                        $element->getFullyQualifiedName(),
                        $content . $separator . $label,
                        $formLabelOptions
                    );
                    break;

                case self::APPEND:
                    // Break was intentionally omitted

                case self::PREPEND:
                    // Break was intentionally omitted

                default:
                    $label = $view->formLabel(
                        $element->getFullyQualifiedName(),
                        $label,
                        $formLabelOptions
                    );
                    break;
            }
        } else {
            $label = '&#160;';
        }

        if (null !== $tag) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            if (null !== $this->_tagClass) {
                $decorator->setOptions(['tag'   => $tag,
                                             'id'    => $id . '-label',
                                             'class' => $tagClass]);
            } else {
                $decorator->setOptions(['tag'   => $tag,
                                             'id'    => $id . '-label']);
            }

            $label = $decorator->render($label);
        }

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;

            case self::PREPEND:
                return $label . $separator . $content;

            case self::IMPLICIT:
                // Break was intentionally omitted

            case self::IMPLICIT_PREPEND:
                // Break was intentionally omitted

            case self::IMPLICIT_APPEND:
                return $label;
        }
    }
}
