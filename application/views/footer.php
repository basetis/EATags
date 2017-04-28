        <hr>
<?php

    // DEFINE FOOTER BTNS
    // KEY MUST BE THE URI
    // REMEMBER THAT ROUTES COULD BE USED FOR HIDDEN REDIRECT
    $footer_btns = array( );
    $footer_btns[$this->lang->line('footer_link_contact')] = array(
        'class' => 'btn',
        'title' => $this->lang->line('footer_contact'),
    );
    $footer_btns[$this->lang->line('footer_link_legal')] = array(
        'class' => 'btn',
        'title' => $this->lang->line('footer_tos'),
    );
    $footer_btns[$this->lang->line('footer_link_privacy')] = array(
        'class' => 'btn',
        'title' => $this->lang->line('footer_privacy'),
    );
    $footer_btns[$this->lang->line('footer_link_cookies')] = array(
        'class' => 'btn',
        'title' => $this->lang->line('footer_cookies'),
    );
    $footer_btns2[$this->lang->line('footer_link_faqs')] = array(
        'class' => 'btn',
        'title' => $this->lang->line('footer_faq'),
    );
    $footer_btns2[$this->lang->line('footer_link_about')] = array(
        'class' => 'btn',
        'title' => $this->lang->line('footer_about'),
    );
    $footer_btns2[$this->lang->line('footer_link_eateam')] = array(
        'class' => 'btn',
        'title' => $this->lang->line('footer_eateam'),
    );




    // ADD ACTIVE CLASS TO FOOTER BTNS WHEN NECESSARY
    if( isset($current_page) && isset($footer_btns[$current_page])){
        $footer_btns[$current_page]['class'] .= ' active';

    }
?>

        <footer>
            <p class="pull-left">
                © EATags <?php echo date("Y"); ?>


            </p>
            <div class="pull-right" id="footer_links_container">
                <div class="pull-right footer_links">
<?php
                foreach ($footer_btns2 as $key => $properties) { ?>

                    <a href="<?php echo base_url($key); ?>"
                        title="<?php echo $properties['title'];?>"><?php echo $properties['title'];?></a>
                        <br/>
<?php
                } ?>
                <a href="https://plus.google.com/103356750378366557946" rel="publisher" target="_blank" title="Google+">Google+</a>
                </div>
                <div class="pull-right footer_links">
<?php
                foreach ($footer_btns as $key => $properties) { ?>

                    <a href="<?php echo base_url($key); ?>"
                        title="<?php echo $properties['title'];?>"><?php echo $properties['title'];?></a>
                        <br/>
<?php
                } ?>
                </div>
            </div>
            <div style="clear:both"></div>
        </footer>
    </div><!--/container-->
       <?php
            $cookies_enabled = $this->input->cookie('eatags_confirm_cookies',TRUE);
            if(isset($cookies_enabled) && strlen($cookies_enabled) > 0 ) {

            echo "<!-- UserVoice plugin BEGIN -->
                <script type='text/javascript'>
                    var uvOptions = {};
                    (function() {
                    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
                    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/5mIju4sLCTng5TM74H4ThA.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
                    })();
                </script>
                <!-- UserVoice plugin END -->";
            }
        ?>
            <script type='text/javascript'>
              $('.lang').on('click', function(e){
                e.preventDefault();
                $('#lang').val($(this).attr('href')).parent().submit();
              });
            </script>
<?php
    // ADD GOOGLE ANALYTICS ONLY AT PRODUCTION ENVIRONMENT
    if (defined('ENVIRONMENT'))
    {
        switch (ENVIRONMENT)
        {
            case 'development':
            case 'testing':
                break;
            case 'production':
                $cookies_enabled = $this->input->cookie('eatags_confirm_cookies',TRUE);
                if(isset($cookies_enabled) && strlen($cookies_enabled) > 0 ) {
                    echo "<script type='text/javascript'>
                            var _gaq = _gaq || [];
                            _gaq.push(['_setAccount', 'UA-33362097-1']);
                            _gaq.push(['_trackPageview']);
                            (function() {
                            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                            })();
                        </script>";
                }
                break;
            default:
                break;
        }
    }?>

</body>
</html>