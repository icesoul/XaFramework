<form id="fileupload" action="server/php/" method="POST" enctype="multipart/form-data">
    <div class="row fileupload-buttonbar">
        <div class="span7">
            <span class="btn btn-success fileinput-button">
                <i class="icon-plus icon-white"></i>
                <span>Add files...</span>
                <input type="file" name="files[]" multiple>
            </span>
        </div>
        <div class="span5">
            <div class="progress progress-success progress-striped active fade">
                <div class="bar" style="width:0%;"></div>
            </div>
        </div>
    </div>
    <div class="fileupload-loading"></div>
    <br>
    <table class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
</form>