<div class="dms-widget dms-widget-unparameterized-action" data-action-label="{{ \Dms\Web\Expressive\Util\ActionLabeler::getActionButtonLabel($action) }}">
    <p>
        <button class="dms-run-action-form btn btn-{{ $class or 'default' }}" data-action="{{ $actionUrl }}" data-method="post">
            {{ \Dms\Web\Expressive\Util\ActionLabeler::getActionButtonLabel($action) }} <i class="fa fa-arrow-right"></i>
        </button>
    </p>
</div>