<?php

class UPME_Html
{
    private function __construct()
    {
        // Nothing to do here
    }

    public static function text_box($property = array())
    {
        $text_box = '<input type="text"';
        
        if(is_array($property))
        {
            foreach($property as $key=>$value)
                $text_box.= ' '.$key.'="'.$value.'"';
        }
        	
        $text_box.=' />';
        return $text_box;
    }

    public static function text_area($property = array())
    {
        $text_area = '<textarea';
        
        if(is_array($property))
        {
            foreach($property as $key=>$value)
            {
                if($key!='value')
                    $text_area.=' '.$key.'="'.$value.'"';
            }    
        }
        
        $text_area.= '>';
        if(isset($property['value']))
            $text_area.= $property['value'];
        	
        $text_area.='</textarea>';
        
        return $text_area;
    }

    public static function drop_down($property=array(),$data=array(),$selected=null)
    {
        
        $drop_down='<select';
        
        if(is_array($property))
        {
            foreach($property as $key=>$value)
                $drop_down.=' '.$key.'="'.$value.'"';
        }

        if(is_array($selected))
            $drop_down.=' multiple="multiple"';
        
        $drop_down.='>';
        $key=null; $value=null;
        
        foreach($data as $key=>$value)
        {
            $sel = '';
            
            if(trim($key) != '')
            {
                if(is_array($selected) && in_array(trim($key),$selected))
                    $sel=' selected="selected"';
                else if(!is_array($selected) && trim($key) == trim($selected))
                    $sel=' selected="selected"';
            }

            $drop_down.='<option value="'.trim($key).'"'.$sel.'>'.trim($value).'</option>';
        }

        $drop_down.='</select>';
        
        return $drop_down;
    }

    public static function radio_button($property=array(),$selected='0')
    {
        $radio_btn='<input type="radio"';
        
        $checked='';
        
        if(is_array($property))
        {
            foreach($property as $key=>$value)
            {
                if($key == 'value' && trim($value) == trim($selected))
                    $checked=' checked="checked"';
            
                $radio_btn.=' '.$key.'="'.$value.'"';
            }    
        }
        
        $radio_btn.=$checked.' />';
        
        return $radio_btn;
    }

    public static function check_box($property=array(),$selected='0')
    {
        $chek_box='<input type="checkbox"';
        	
        $checked='';
        
        if(is_array($property))
        {
            foreach($property as $key=>$value)
            {
                if($key == 'value' && trim($value) == trim($selected))
                    $checked=' checked="checked"';
            
                $chek_box.=' '.$key.'="'.$value.'"';
            }    
        }
        
        $chek_box.=$checked.' />';
        return $chek_box;
    }
    
    public static function button($type='button', $property=array())
    {
        $button='<input type="'.$type.'"';
        
        if(is_array($property))
        {
            foreach($property as $key=>$value)
                $button.=' '.$key.'="'.$value.'"';
        }
        
        $button.= ' />';
        
        return $button;
    }

}
?>