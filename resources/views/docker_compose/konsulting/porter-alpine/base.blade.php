version: "3.2"

networks:
  porter:
    driver: bridge

services:

  @include("docker_compose.{$imageSet}.dns")

  @include("docker_compose.{$imageSet}.mailhog")

  @include("docker_compose.{$imageSet}.nginx")

  @include("docker_compose.{$imageSet}.node")

@foreach($activePhpVersions as $key => $version)
  # PHP version {!! $version->version_number !!}

  @include("docker_compose.{$imageSet}.php_fpm")

  @include("docker_compose.{$imageSet}.php_cli")

  # END PHP version {!! $version->version_number !!}
@endforeach

@if($useMysql)
  @include("docker_compose.{$imageSet}.mysql")
@endif

@if($useRedis)
  @include("docker_compose.{$imageSet}.redis")
@endif
