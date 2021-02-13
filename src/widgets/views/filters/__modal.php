<div class="modal fade charts-add-filter modal-add-filter-<?= $id ?>" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Применить фильтр</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label>Доступные фильтры</label>
                <ul class="filters-list" style="border: 1px dotted #999;"></ul>

                <div class="filter-data" style="border: 1px dotted #999; padding: 10px;">
                    <label>Выберите значение</label>
                    <div class="sel-values" style="display: none">
                        <select name="values[]" class="filter-values"></select>
                    </div>
                    <div class="text-values" style="display: none">
                        <input type="text" class="form-control" value="" style="color: #999!important; border: 1px dotted #999!important; border-radius: 0px!important; padding: 6px!important; font-size: 14px!important;" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-default add-filter-modal-button">Добавить</button>
            </div>
        </div>
    </div>
</div>
