<div class="row">
    <div class="col-xs-4">
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <tbody>
                    <?php
                        foreach($columns as $column=>$friendlyName) {
                            echo 
                                '<tr>'
                                    . '<td>' . $friendlyName . '</td>'
                                    . '<td><input type="text" class="form-control" '
                                            . 'name="' . $column . '" '
                                            . 'id="' . $column . '" '
                                            . 'value="' . $model->{$column} . '" ' 
                                            . ($enabled ? '' : 'disabled="disabled"' ) . ' />'
                                    . '</td>'
                                . '</tr>';                            
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
