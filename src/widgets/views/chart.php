<div style="border: 1px dotted #999; padding: 10px; height: 500px!important; margin-bottom: 20px; display: block; border-radius: 10px;">

    <div class="chart chart-<?= $id ?>">
        <div class="dates-filters" style="border-bottom: 1px dotted #999;">
            <?= $this->render( 'filters/__dates', [ 'id' => $id, 'chart_id' => $chart_id, 'settings' => $settings ]) ?>

            <div class="dates-interval" style="display: none">
                <div class="form-group">
                    <label class="form-control-label">Фильтраци по дате</label>
                    <input type="text" name="range" value="" class="form-control pull-right"  readonly
                           style="color: #999!important; border: 1px dotted #999!important; font-size: 13px!important; border-radius: 0px!important; width: 300px!important;"/>
                </div>
            </div>
        </div>
        <div class="data-filters" style="border-bottom: 1px dotted #999; padding-bottom:10px; margin-top: 10px;">
            <button type="button" class="btn btn-default btn-m add-filter-button" data-chart-id="<?= $chart_id ?>"><i class="ni ni-settings-gear-65"></i> &nbsp; Применить фильтр</button>
            <button type="button" class="btn btn-default btn-m update-filter-button" data-chart-id="<?= $chart_id ?>"><i class="ni ni-settings-gear-65"></i> &nbsp; Обновить график</button>
            <div class="applied-filters" style="height: 20px;"></div>
        </div>
        <div class="holder" id="<?= $id ?>" style="height: 355px;"></div>
    </div>

    <?= $this->render( 'filters/__modal', [ 'id' => $id, 'chart_id' => $chart_id, 'settings' => $settings ]) ?>
</div>