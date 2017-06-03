<?php
/**
 * InputHtmlBuilder.php
 */

namespace SoftnCMS\util\form\builders;

use SoftnCMS\util\form\InputHtml;
use SoftnCMS\util\form\inputs\builders\InputBuilderInterface;
use SoftnCMS\util\form\inputs\builders\InputTextBuilder;

/**
 * Class InputHtmlBuilder
 * @author Nicolás Marulanda P.
 */
class InputHtmlBuilder extends InputTextBuilder implements InputBuilderInterface {
    
    public function __construct($name, $type) {
        $this->name = $name;
        $this->type = $type;
        $this->initValue();
    }
    
    public static function init($name, $type = 'text') {
        return new InputHtmlBuilder($name, $type);
    }
    
    public function build() {
        return new InputHtml($this);
    }
}
