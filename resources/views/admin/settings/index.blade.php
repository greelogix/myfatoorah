<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFatoorah Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>MyFatoorah Settings</h1>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('myfatoorah.admin.settings.store') }}" method="POST">
                    @csrf
                    
                    @foreach($settings as $group => $groupSettings)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">{{ ucfirst($group) }} Settings</h5>
                            </div>
                            <div class="card-body">
                                @foreach($groupSettings as $setting)
                                    <div class="mb-3">
                                        <label for="setting_{{ $setting->id }}" class="form-label">
                                            {{ $setting->key }}
                                            @if($setting->description)
                                                <small class="text-muted">({{ $setting->description }})</small>
                                            @endif
                                        </label>
                                        
                                        @if($setting->type === 'textarea')
                                            <textarea 
                                                name="settings[{{ $setting->id }}][value]" 
                                                id="setting_{{ $setting->id }}"
                                                class="form-control"
                                                rows="3"
                                            >{{ old('settings.'.$setting->id.'.value', $setting->value) }}</textarea>
                                        @elseif($setting->type === 'boolean')
                                            <select 
                                                name="settings[{{ $setting->id }}][value]" 
                                                id="setting_{{ $setting->id }}"
                                                class="form-select"
                                            >
                                                <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Yes</option>
                                                <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>No</option>
                                            </select>
                                        @elseif($setting->type === 'password')
                                            <input 
                                                type="password" 
                                                name="settings[{{ $setting->id }}][value]" 
                                                id="setting_{{ $setting->id }}"
                                                class="form-control"
                                                value="{{ old('settings.'.$setting->id.'.value', $setting->value) }}"
                                                placeholder="Enter {{ str_replace('_', ' ', $setting->key) }}"
                                            >
                                            @if($setting->value)
                                                <small class="text-muted">Current value is set (hidden for security)</small>
                                            @endif
                                        @else
                                            <input 
                                                type="text" 
                                                name="settings[{{ $setting->id }}][value]" 
                                                id="setting_{{ $setting->id }}"
                                                class="form-control"
                                                value="{{ old('settings.'.$setting->id.'.value', $setting->value) }}"
                                            >
                                        @endif
                                        
                                        <input type="hidden" name="settings[{{ $setting->id }}][key]" value="{{ $setting->key }}">
                                        <input type="hidden" name="settings[{{ $setting->id }}][type]" value="{{ $setting->type }}">
                                        <input type="hidden" name="settings[{{ $setting->id }}][group]" value="{{ $setting->group }}">
                                        <input type="hidden" name="settings[{{ $setting->id }}][description]" value="{{ $setting->description }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

