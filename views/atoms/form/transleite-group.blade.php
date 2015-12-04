<fieldset class="text-center" style="border-bottom: 1px solid grey">
    <legend>{{ $groupTitle }}</legend>
    @foreach ($group as $childLineKey => $childLine)
        @if(is_array($childLine))
            @include('transleite::atoms.form.transleite-group', [
                'groupTitle' => $groupTitle . ' | ' .$lineKey,
                'group' => $childLine,
                'formElementID' => "{$formElementID}[{$childLineKey}]"
            ])
        @else
            <div class="form-group transleite-group">
                <label for="{!! "translations[$langKey]" !!}{!! $formElementID !!}[{{ $childLineKey }}]"
                       class="control-label col-lg-4">{{ $childLineKey }}</label>

                <div class="col-lg-8">
        <textarea id="{!! "translations[$langKey]" !!}{!! $formElementID !!}[{{ $childLineKey }}]"
                  name="{!! "translations[$langKey]" !!}{!! $formElementID !!}[{{ $childLineKey }}]"
                  class="form-control col-lg-8">{!! $childLine !!}</textarea>
                </div>
            </div>
        @endif
    @endforeach
</fieldset>