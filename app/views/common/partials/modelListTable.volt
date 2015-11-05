<script>
    function confirmModelDelete(id) {
        ret = confirm('Are you sure you want to delete entry # ' + id + '?');
        if(ret) {
            document.getElementById('modelForm_' + id).submit();
        }
    }
</script>

<div class="row">
    <div class="col-xs-12">
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table id="{{ name|e }}Table" class="table table-hover">
                <thead>
                    <?php
                        foreach($columns as $column=>$friendlyName) {
                            echo '<th>' . $friendlyName . '</th>';                            
                        }
                        echo '<th>Actions</th>';
                    ?>
                </thead>
                <tbody>
                    <?php
                        foreach($models as $model) {
                            echo '<tr>';
                            foreach($columns as $column=>$friendlyName) {
                                echo '<td>' . $model->{$column} . '</td>';                            
                            }
                            echo '<td>'
                                    . '<a href="' . $this->url->get($name . '/search/' . $model->id) . '">'
                                        . '<button type="button" class="btn btn-sm btn-default">Show</button>'
                                    . '</a> '
                                    . '<a href="' . $this->url->get($name . '/edit/' . $model->id) . '">'
                                        . '<button type="button" class="btn btn-sm btn-success">Edit</button>'
                                    . '</a> '
                                    . '<button type="button" class="btn btn-sm btn-danger" onclick="confirmModelDelete(' . $model->id . ')">Delete</button>'
                                    . Phalcon\Tag::form(array($name . '/delete/' . $model->id, 'method' => 'post', 'id'=>'modelForm_' . $model->id))
                                    . $this->tag->endForm()
                                    
                                . '</td>'
                            . '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#{{ name|e }}Table').dataTable();
});
</script>