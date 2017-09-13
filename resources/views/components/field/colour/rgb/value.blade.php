<div class="dms-display-colour">
    <div class="col-xs-2" style="background-color: {{ $value }}; border-radius: 3px">
        &nbsp;
    </div>
    <div class="col-xs-10">
        {{ $value }} - {{ Dms\Common\Structure\Colour\Colour::fromRgbString($value)->toHexString() }}
    </div>
</div>