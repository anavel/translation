<fieldset>
    <legend>{{ $groupTitle }}</legend>
    @foreach ($group as $childLineKey => $childLine)
        @if(is_array($group))
            @include('transleite::atoms.form.transleite-group', [
                'groupTitle' => $groupTitle . ' | ' .$lineKey,
                'group' => $childLine,
                'formElementID' => "{$formElementID}[{$langKey}]"
            ])
        @else
            <div class="form-group transleite-group">
                <label for="{!! $formElementID !!} }}[{{ $childLineKey }}]"
                       class="control-label col-lg-4">{{ $childLineKey }}</label>

        <textarea id="{!! $formElementID !!} }}[{{ $childLineKey }}]"
                  name="{!! $formElementID !!} }}[{{ $childLineKey }}"
                  class="form-control col-lg-8">{!! $childLine !!}</textarea>
            </div>
        @endif
    @endforeach
</fieldset>