version: "3.2"

networks:
  porter:
    driver: bridge

services:

  @include("{$imageSet}::dns")

  @include("{$imageSet}::mailhog")

  @include("{$imageSet}::nginx")

  @include("{$imageSet}::node")

@foreach($activePhpVersions as $key => $version)
  # PHP version {!! $version->version_number !!}

  @include("{$imageSet}::php_fpm")

  @include("{$imageSet}::php_cli")

  # END PHP version {!! $version->version_number !!}
@endforeach

@if($useMysql)
  @include("{$imageSet}::mysql")
@endif

@if($useRedis)
  @include("{$imageSet}::redis")
@endif

@if ($useBrowser)
  @include("{$imageSet}::browser")
@endif
