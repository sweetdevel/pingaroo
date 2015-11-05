<?php if(isset($messages['error'])) { ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> Error!</h4>
        <?php
            foreach($messages['error'] as $message) {
                echo $message . '<br />';
            }
        ?>
    </div>
<?php } ?>