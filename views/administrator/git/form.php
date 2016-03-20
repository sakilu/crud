<?= $this->form->form_open() ?>
<div class="panel-body">
    <div class="form-group">
        <div class="col-lg-8">
            <div class="bs-component">
                <?= $repo->getStatusOutput() ?>
            </div>
        </div>
    </div>
</div>
</form>