@inject('config', 'Illuminate\Config\Repository')
@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

<script>
    window.Dms.config = {
        debug: {!! json_encode($config->get('app.debug', false)) !!},
        csrf: {
            token: {!! json_encode(csrf_token()) !!}
        },
        routes: {
            loginUrl: {!! json_encode($urlHelper->generate('dms::auth.login')) !!},
            localUrls: {
                root: {!! json_encode($urlHelper->generate('dms::index')) !!},
                exclude: [
                    {!! json_encode($urlHelper->generate('dms::auth.logout')) !!}
                ]
            },
            downloadFile: function (token) {
                return {!! json_encode($urlHelper->generate('dms::file.download', ['token' => '__token__'])) !!}.replace('__token__', token);
            }
        }
    };
</script>
