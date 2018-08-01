version: "3.2"

networks:
  porter:
    driver: bridge

services:

  @include('docker_compose.dns')

  @include('docker_compose.mailhog')

  @include('docker_compose.nginx')

  @include('docker_compose.node')

@foreach($activePhpVersions as $key => $version)
  # PHP version {!! $version->version_number !!}

  @include('docker_compose.php_fpm')

  @include('docker_compose.php_cli')

  # END PHP version {!! $version->version_number !!}
@endforeach

@if($useMysql)
  @include('docker_compose.mysql')
@endif

@if($useRedis)
  @include('docker_compose.redis')
@endif
