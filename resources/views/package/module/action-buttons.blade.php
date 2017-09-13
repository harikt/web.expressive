<?php /** @var \Dms\Web\Expressive\Renderer\Action\ActionButton[] $actionButtons */ ?>

@foreach($actionButtons as $actionButton)
    @if($actionButton->isDisabled())
        <a class="btn btn-{{ \Dms\Web\Expressive\Util\KeywordTypeIdentifier::getClass($actionButton->getName()) ?? 'default' }}"
           href="javascript:void(0)" disabled="disabled">
            {{ $actionButton->getLabel() }}
        </a>
    @elseif($actionButton->isPost())
        <div class="dms-run-action-form inline" data-action="{{ $actionButton->getUrl($objectId) }}" data-method="post" data-reload-page-after-submit="1">
            {!! csrf_field() !!}
            <button type="submit"
                    class="btn btn-{{ \Dms\Web\Expressive\Util\KeywordTypeIdentifier::getClass($actionButton->getName()) ?? 'default' }}">
                {{ $actionButton->getLabel() }}
            </button>
        </div>
    @else
        <a class="btn btn-{{ \Dms\Web\Expressive\Util\KeywordTypeIdentifier::getClass($actionButton->getName()) ?? 'default' }}"
           href="{{ $actionButton->getUrl($objectId) }}">
            {{ $actionButton->getLabel() }}
        </a>
    @endif
@endforeach