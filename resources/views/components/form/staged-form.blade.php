<?php /** @var \Dms\Web\Expressive\Http\ModuleContext $moduleContext */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\FormRenderingContext $renderingContext */ ?>
<?php /** @var \Dms\Core\Module\IAction $action */ ?>
<?php /** @var \Dms\Core\Form\IStagedForm $stagedForm */ ?>
<?php /** @var \Dms\Web\Expressive\Renderer\Form\FormRendererCollection $formRendererCollection */ ?>
<?php /** @var array $hiddenValues */ ?>
<div class="dms-staged-form-container">
    <div
		<?php /* 'object_id' => $renderingContext->getObject() ? [ 'object_id' => $renderingContext->getObjectId()] : */ ?>
            data-reload-form-url="{{ $moduleContext->getUrl('action.form', ['package' => $moduleContext->getModule()->getPackageName(), 'module' => $moduleContext->getModule()->getName(), 'action' => $actionName]) }}"
            data-reload-page-after-submit="1"
            data-action="{{ $moduleContext->getUrl('action.run', ['package' => $moduleContext->getModule()->getPackageName(), 'module' => $moduleContext->getModule()->getName(), 'action' => $actionName]) }}"
            data-method="post"
            data-enctype="multipart/form-data"
            class="dms-staged-form form-horizontal"
    >
        <input type="hidden" name="_token" value="{!! csrf_token() !!}">

        @if($hiddenValues)
            <div class="dms-form-stage-container loaded hidden">
                <div class="dms-form-stage dms-form-stage-known-data">
                    @foreach($hiddenValues ?? [] as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}"/>
                    @endforeach
                </div>
            </div>
        @endif

        <?php $currentData = [] ?>
        @for ($stageNumber = 1; $stageNumber <= $stagedForm->getAmountOfStages(); $stageNumber++)
            <?php $absoluteStageNumber =  $stageNumber + ($initialStageNumber ?? 1) - 1 ?>
            <?php $renderingContext->setCurrentStageNumber($absoluteStageNumber) ?>
            <?php $stage = $stagedForm->getStage($stageNumber) ?>
            @if ($stage instanceof \Dms\Core\Form\Stage\IndependentFormStage)
                <?php $form = $stage->loadForm() ?>
                <div class="dms-form-stage-container loaded">
                    <div class="dms-form-stage" data-stage-number="{{ $stageNumber }}">
                        {!!  $formRendererCollection->findRendererFor($renderingContext, $form)->renderFields($renderingContext, $form) !!}
                    </div>
                </div>
                <?php $currentData += $form->getInitialValues() ?>
            @else
                <?php $form = $stagedForm->tryLoadFormForStage($stageNumber, $currentData, true) ?>
                <div class="dms-form-stage-container {{ $form ? 'loaded' : '' }}">
                    <div
                            class="dms-form-stage dms-dependent-form-stage"
                            data-stage-number="{{ $stageNumber }}"
                            data-load-stage-url="{{ $moduleContext->getUrl('action.form.stage', ['package' => $moduleContext->getModule()->getPackageName(), 'module' => $moduleContext->getModule()->getName(), 'action' => $actionName, 'stage' => $absoluteStageNumber]) }}"
                            @if($stage->getRequiredFieldNames() !== null)
                            data-stage-dependent-fields="{{ json_encode($stage->getRequiredFieldNames()) }}"
                            @endif
                            data-stage-dependent-fields-stage-map="{{ json_encode($stagedForm->getRequiredFieldGroupedByStagesForStage($stageNumber)) }}"
                    >
                        @if ($form)
                            {!!  $formRendererCollection->findRendererFor($renderingContext, $form)->renderFields($renderingContext, $form) !!}
                            <?php $currentData += $form->getInitialValues() ?>
                        @else
                            <div class="row">
                                <div class="col-lg-offset-2 col-lg-10 col-md-offset-3 col-md-9 col-sm-offset-4 col-sm-8">
                                    <p class="help-block">
                                        The following fields are not shown because they require you to enter the values
                                        for the previous fields in this form.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                    @include('dms::partials.spinner')
                </div>
            @endif
        @endfor

        <button class="btn btn-{{ \Dms\Web\Expressive\Util\KeywordTypeIdentifier::getClass($action->getName(), 'primary') }}"
                type="submit">
            {{ \Dms\Web\Expressive\Util\ActionLabeler::getSubmitButtonLabel($action) }}
            <i class="fa fa-arrow-right"></i>
        </button>

    </div>
    @include('dms::partials.spinner')
</div>
