<?php

/*
    $x=ob_get_contents();
    ob_end_clean();
    echo replacePngTags($x);

function replacePngTags($x){
    // make sure that we are only replacing for the Windows versions of Internet 
    // Explorer 5+, and not Opera identified as MSIE 
    $msie='/msie\s([5-6])\.?[0-9]*.*(win)/i'; 
    $opera='/opera\s+[0-9]+/i'; 
    $arr2 = '';
    $images = '';
    if(!isset($_SERVER['HTTP_USER_AGENT']) || 
        !preg_match($msie,$_SERVER['HTTP_USER_AGENT']) || 
        preg_match($opera,$_SERVER['HTTP_USER_AGENT'])) 
        return $x; 
    
    // OK, time to find all the IMG tags with ".png" in them
    preg_match_all('/<img.*\.png.*>/Ui',$x,$images);
    while(list($imgnum,$v)=@each($images[0])){
        $original=$v;
        $atts=''; $width=0; $height=0;
        // If the size is defined by styles, find 
        preg_match_all('/style=".*(width: ([0-9]+))px.*'.
                        '(height: ([0-9]+))px.*"/Ui',$v,$arr2);
        if(is_array($arr2) && count($arr2[0])){
            // size was defined by styles, get values
            $width=$arr2[2][0];
            $height=$arr2[4][0];
        }
        // size was not defined by styles, get values
        preg_match_all('/width=\"?([0-9]+)\"?/i',$v,$arr2);
        if(is_array($arr2) && count($arr2[0])){
            $width=$arr2[1][0];
        }
        preg_match_all('/height=\"?([0-9]+)\"?/i',$v,$arr2);
        if(is_array($arr2) && count($arr2[0])){
            $height=$arr2[1][0];
        }
        preg_match_all('/src=\"([^\"]+\.png)\"/i',$v,$arr2);
        if(isset($arr2[1][0]) && !empty($arr2[1][0]))
            $image=$arr2[1][0];
        else
            $image=NULL;

        // We do this so that we can put our spacer.gif image in the same
        // directory as the image
        $tmp=split('[\\/]',$image);
        array_pop($tmp);
        $image_path=join('/',$tmp);
        if(strlen($image_path)) $image_path.='/';

        if($width!=0 && $height!=0) {
        // end quote is already supplied by originial src attribute
        $replace_src_with=$image_path.'spacer.gif" style="width: '.$width.
            'px; height: '.$height.'px; filter: progid:DXImageTransform.'.
            'Microsoft.AlphaImageLoader(src=\''.$image.'\', sizingMethod='.
            '\'scale\')';
        }
        else {
        $replace_src_with=$image_path.'spacer.gif" style="
            filter: progid:DXImageTransform.'.
            'Microsoft.AlphaImageLoader(src=\''.$image.'\')';
        }

        
        // now create the new tag from the old
        $new_tag=str_replace($image,$replace_src_with,$original);
        
        // now place the new tag into the content
        $x=str_replace($original,$new_tag,$x);
    }
    return $x;
}
*/
?>
