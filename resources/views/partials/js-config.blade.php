<script>
    window.Dms.config = {
        debug: {!! json_encode(config('app.debug', false)) !!},
        csrf: {
            token: {!! json_encode(csrf_token()) !!}
        },
        routes: {
            loginUrl: {!! json_encode(route('dms::auth.login')) !!},
            localUrls: {
                root: {!! json_encode(route('dms::index')) !!},
                exclude: [
                    {!! json_encode(route('dms::auth.logout')) !!}
                ]
            },
            downloadFile: function (token) {
                return {!! json_encode(route('dms::file.download', ['token' => '__token__'])) !!}.replace('__token__', token);
            }
        }
    };
</script>
