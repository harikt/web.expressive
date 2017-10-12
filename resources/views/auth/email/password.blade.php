@inject('urlHelper', 'Zend\Expressive\Helper\UrlHelper')

Click here to reset your password: {{ $urlHelper->generate('dms::auth.password.reset', ['token' => $token]) }}
