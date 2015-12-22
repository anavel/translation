@extends('adoadomin::layouts.master')

@section('content-header')
    <h1>
        {{ config('adoadomin-transleite.name') }}
        <small>{{ trans('transleite::messages.edit_title') }}</small>
    </h1>
@stop

@section('content')
    @if(! empty($editLangs))
        <form method="post" action="" id="transleite-form">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_method" value="PUT">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <?php $i = 0?>
                    @foreach (array_keys($editLangs) as $locale)
                        <li role="presentation" class="{{ $i === 0 ? 'active' : ''}}">
                            <a href="{{ '#' . $locale }}" role="tab" data-toggle="tab"
                               aria-controls="{{ $locale }}">{{ $locale }}</a>
                        </li>
                        <?php $i++?>
                    @endforeach

                    <li class="pull-right">
                        <button type="submit" class="btn btn-primary"><i
                                    class="fa fa-save"></i> {{ trans('transleite::messages.save_button') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <?php $i = 0?>
                    @foreach($editLangs as $langKey => $langLines)
                        <div role="tabpanel" class="tab-pane {{ $i === 0 ? 'active' : ''}}" id="{{ $langKey }}">
                            <div class="form-horizontal">
                                @foreach ($langLines as $lineKey => $line)
                                    @if(is_array($line))
                                        @include('transleite::atoms.form.transleite-group', [
                                            'groupTitle' => $lineKey,
                                            'group' => $line,
                                            'formElementID' => "[$lineKey]"
                                        ])
                                    @else
                                        <div class="form-group transleite-group">
                                            <label for="translations[{{ $langKey }}][{{ $lineKey }}]"
                                                   class="control-label col-lg-4 {{ ! empty($editLangsMissingKeys[$langKey]) && in_array($lineKey,$editLangsMissingKeys[$langKey]) ? 'text-primary' : '' }}">{{ $lineKey }}</label>

                                            <div class="col-lg-8">
                                                <textarea id="translations[{{ $langKey }}][{{ $lineKey }}]"
                                                          name="translations[{{ $langKey }}][{{ $lineKey }}]"
                                                          class="form-control">{!! $line !!}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="box-footer clearfix">
                                <button type="submit" class="btn btn-primary pull-right"><i
                                            class="fa fa-save"></i> {{ trans('transleite::messages.save_button') }}
                                </button>
                            </div>
                        </div>
                        <?php $i++?>
                    @endforeach
                </div>
            </div>
        </form>

        <form action="{{ route('transleite.file.create', [Route::input('param'), Route::input('param2')]) }}"
              method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <fieldset class="panel">
                <legend>{{ trans('transleite::messages.new_line') }}</legend>
                <div class="form-horizontal panel-body">
                    <div class="form-group transleite-group">
                        <label for="translations-new[key]" class="control-label col-lg-4">{{ trans('transleite::messages.new_line_key_label') }}</label>

                        <div class="col-lg-8">
                            <input type="text" id="translations-new[key]" name="translations-new[key]"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="form-group transleite-group">
                        <label for="translations-new[value]" class="control-label col-lg-4">{{ trans('transleite::messages.new_line_key_label') }}</label>

                        <div class="col-lg-8">
                                <textarea id="translations-new[value]" name="translations-new[value]"
                                          class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="panel-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary"><i
                                    class="fa fa-save"></i> {{ trans('transleite::messages.new_button') }}
                        </button>
                    </div>
                </div>
            </fieldset>
        </form>
    @endif
@stop