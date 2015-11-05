<?php if(isset($messages['success'])) { ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> Success!</h4>
        <?php
            foreach($messages['success'] as $message) {
                echo $message . '<br />';
            }
        ?>
    </div>
<?php } ?>