<?php
  // PAGE CONTENT - HOME PAGE
?>

    <div class="row" id="main_slogan">
      <h1><?= $this->lang->line('home_header'); ?></h1>
    </div><!--/row-->

  </div><!--/container-->

  <div id="three_steps_bar">
    <div class="container">

      <div class="step_void">

      </div><!--/step_void-->

      <div class="step">
        <span class="step_number">1</span>
        <p class="step_txt">
          <a class="btn eat-btn-success" href="<?php echo base_url($this->lang->line('header_link_auth_register'));?>"><?= $this->lang->line('home_signup') ?></a>
        </p>
      </div><!--/step-->

      <div class="step">
        <span class="step_number">2</span>
        <p class="step_txt"><?= $this->lang->line('home_login_to_evernote') ?></p>
      </div><!--/step-->

      <div class="step">
        <span class="step_number">3</span>
        <p class="step_txt"><?= $this->lang->line('home_configure_tags') ?></p>
      </div><!--/step-->
      <div class="secondari_slogan">
        <p><?= $this->lang->line('home_need_it_type_eat') ?></p>
      </div><!--/secondari_slogan-->

    </div><!--/container-->
    <div style="clear:both"></div>

  </div><!--/three_steps_bar-->

  <div id="examples_bar">
    <div class="container">
      <div class="example_title">
       <!-- some examples-->
       <!-- js adds the toggle examples buttons here -->
      </div><!--/example_title-->

      <div class="example_tag" id="example_1">
        <div class="example_label">
          eat.wordpress.post
        </div><!--/example_label-->
        <div class="example_photo">
          <img src="/assets/images/photos/eat_wp.png">
        </div><!--/example_photo-->
        <div class="example_txt">
          <?= $this->lang->line('home_example_1') ?>
        </div><!--/example_txt-->
      </div><!--/example_tag-->

      <div class="example_tag" id="example_2">
        <div class="example_label">
          eat.tweet
        </div><!--/example_label-->
        <div class="example_photo">
          <img src="/assets/images/photos/eat_tweet.png">
        </div><!--/example_photo-->
        <div class="example_txt">
          <?= $this->lang->line('home_example_2') ?>
        </div><!--/example_txt-->
      </div><!--/example_tag-->

      <div class="example_tag" id="example_3">
        <div class="example_label">
          eat.toc
        </div><!--/example_label-->
        <div class="example_photo">
          <img src="/assets/images/photos/eat_toc.png">
        </div><!--/example_photo-->
        <div class="example_txt">
          <?= $this->lang->line('home_example_3') ?>
        </div><!--/example_txt-->
      </div><!--/example_tag-->

      <div class="example_tag" id="example_4">
        <div class="example_label">
          eat.latex
        </div><!--/example_label-->
        <div class="example_photo">
          <img src="/assets/images/photos/eat_latex.png">
        </div><!--/example_photo-->
        <div class="example_txt">
          <?= $this->lang->line('home_example_4') ?>
        </div><!--/example_txt-->
      </div><!--/example_tag-->


      <div style="clear:both"></div>

    </div><!--/container-->
  </div><!--/examples_bar-->
  <script type="text/javascript">
    $(function(){

      if( $('.example_tag').length ){
        var examples_animation = function(num){
          if(!stop_examples_animation){
            num++;
            if(num > $('.example_tag').length){
              num = 1;
            }
            $('.link_to_example').removeClass('active_link_to_example');
            $('#link_to_example_'+num).addClass('active_link_to_example');
            $('#example_'+num).fadeIn('slow');
            $('#example_'+num).delay(10000).hide(function(){examples_animation(num);});
          }
        }
        var stop_examples_animation = 0;

        $('.example_tag').each(function(index, element){
          var ball = document.createElement('span');
            $(ball).addClass('link_to_example');
            $(ball).attr('id', 'link_to_' + element.id);
            $(ball).click(function(){
              if(! $(this).hasClass('active_link_to_example') ){
                stop_examples_animation = 1;
                $('.link_to_example').removeClass('active_link_to_example');
                $('.example_tag').hide();
                $('#'+this.id.replace('link_to_','')).fadeIn();
                $(this).addClass('active_link_to_example');
              }
            })
          $('.example_title').append(ball)
        });

        examples_animation(1);
      }


    });
  </script>

  <div style="clear:both"></div>

  <div class="container">
    <div id="vid_banner">
      <p><?= $this->lang->line('home_tired') ?></p>
      <a target="_blank" href="https://vimeo.com/45086348" class="btn btn-inverse"><?= $this->lang->line('home_watch') ?></a>
    </div>
  </div><!--/container-->

  <div class="container">
    <?php
  $cookies_enabled = $this->input->cookie('eatags_confirm_cookies',TRUE);
  if(isset($cookies_enabled) && strlen($cookies_enabled) > 0 ) {

    echo '<!-- AddThis Button BEGIN -->';
    echo  '<div class="addthis_toolbox addthis_default_style ">';
    echo    '<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>';
          echo '<a href="https://twitter.com/share" class="twitter-share-button" data-text="';
            echo $this->lang->line('home_tweet_text');
            echo '">';
            echo $this->lang->line('home_tweet');
          echo '</a>';

          echo '<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>';
        echo '</div>';

    echo '<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4ff154921ed63017"></script>';
    echo '<!-- AddThis Button END -->';
    echo '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
  }
?>
