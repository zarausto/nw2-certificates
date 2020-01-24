<?php
//header("Content-type:application/pdf");
 if (have_posts()) : 
 while (have_posts()) : the_post(); 
 the_content(); 
 endwhile;
 endif;
