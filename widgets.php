<?php

/**
 * Inits all widgets
 * @since 0.2
 */
function QuBicIdioma_widgets_init() {
    register_widget('QuBicIdioma_Chooser_Widget');
    register_widget('QuBicIdioma_Chooser_Text_Widget');
}

class QuBicIdioma_Chooser_Widget extends WP_Widget {

    /**
     * processes the widget
     */
    function QuBicIdioma_Chooser_Widget() {
        $widget_ops = array(
            'classname' => 'QuBic_Idioma',
            'description' => __('Shows a language selector as an accessible selectbox based in jQuery', QBC_IDIOMA_TEXT_DOMAIN)
        );
        $this->WP_Widget('QuBic_Idioma', __('Language chooser', QBC_IDIOMA_TEXT_DOMAIN), $widget_ops);
    }

    function form($instance) {
        parent::form($instance);
    }

    function update($new_instance, $old_instance) {
        parent::update($new_instance, $old_instance);
    }

    function widget($args, $instance) {
        global $blog_id;
        $llista = QuBicIdioma_obtenir_blocs_actius();
        $output = '<div class="QuBic_Idioma_selector_container">';
        $output.='<form action="#">';
        $output.='<select id="QuBic_Idioma_selector" onchange="QuBic_Idioma_selectorOnChange(this.options[this.selectedIndex].value);">';
        foreach ($llista as $linia):
            $output.='<option ';
            if ($linia['blog_id'] == $blog_id):
                $output.='selected ';
            endif;
            $output.='value="';
            $output.=QuBicIdioma_crearURL($linia['domain'], $linia['path']);
            $output.='">';
            $output.=$linia['language'];
            $output.='</option>';
        endforeach;
        $output.='</select>';
        $output.='</form>';
        $output.='</div>';
        echo $output;
    }

}

/**
 * Text Only Widget
 * @since 0.7 
 */
class QuBicIdioma_Chooser_Text_Widget extends WP_Widget {

    /**
     * processes the widget
     */
    function QuBicIdioma_Chooser_Text_Widget() {
        $widget_ops = array(
            'classname' => 'QuBic_Idioma_Text',
            'description' => __('Shows a language selector as text', QBC_IDIOMA_TEXT_DOMAIN)
        );
        $this->WP_Widget('QuBic_Idioma_Text', __('Language text chooser', QBC_IDIOMA_TEXT_DOMAIN), $widget_ops);
    }

    function form($instance) {
        parent::form($instance);
    }

    function update($new_instance, $old_instance) {
        parent::update($new_instance, $old_instance);
    }

    function widget($args, $instance) {
        global $blog_id;
        $llista = QuBicIdioma_obtenir_blocs_actius();
        $output = '<div class="qibdip_Idioma_Text">';
        foreach ($llista as $linia):
            if ($linia['blog_id'] == $blog_id):
                $output_inici = '<span class="qibdip_idioma_actual">';
                $output_fi = '</span>';
            else:
                $output_inici = '<a href="' . QuBicIdioma_crearURL($linia['domain'], $linia['path']) . '">';
                $output_fi = '</a>';
            endif;
            $output.='&nbsp;';
            $output.=$output_inici;
            $output.=$linia['language'];
            $output.=$output_fi;
        endforeach;
        $output.='</div>';
        echo $output;
    }

}

?>
