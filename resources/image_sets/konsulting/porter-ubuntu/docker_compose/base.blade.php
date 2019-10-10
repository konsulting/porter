version: "3.2"

networks:
  porter:
    driver: bridge

services:

  @includeWhen($useDns, "{$imageSet->getName()}::dns")

  @include("{$imageSet->getName()}::mailhog")

  @include("{$imageSet->getName()}::nginx")

  @include("{$imageSet->getName()}::node")

  @include("{$imageSet->getName()}::ngrok")

@foreach($activePhpVersions as $key => $version)
  # PHP version {!! $version->version_number !!}

  @include("{$imageSet->getName()}::php_fpm")

  @include("{$imageSet->getName()}::php_cli")

  # END PHP version {!! $version->version_number !!}
@endforeach

  @includeWhen($useMysql, "{$imageSet->getName()}::mysql")

  @includeWhen($useRedis, "{$imageSet->getName()}::redis")

  @includeWhen($useBrowser, "{$imageSet->getName()}::browser")
