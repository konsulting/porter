version: "3.2"
networks:
  porter:
    driver: bridge
services:
@foreach($activePhpVersions as $key => $version)
  @include('docker_compose.php_fpm')
  @include('docker_compose.php_cli')
@endforeach
  @include('docker_compose.nginx')
  @include('docker_compose.node')
@if($useMysql)
  @include('docker_compose.mysql')
@endif
@if($useRedis)
  @include('docker_compose.redis')
@endif
