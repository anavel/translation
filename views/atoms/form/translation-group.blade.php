<fieldset class="text-center" style="border-bottom: 1px solid grey">
    <legend>{{ $groupTitle }}</legend>
    @foreach ($group as $childLineKey => $childLine)
        @if(is_array($childLine))
            @include('anavel-translation::atoms.form.translation-group', [
                'groupTitle' => $groupTitle . ' | ' .$childLineKey,
                'group' => $childLine,
                'formElementID' => "{$formElementID}[{$childLineKey}]"
            ])
        @else
            <div class="form-group translation-group">
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