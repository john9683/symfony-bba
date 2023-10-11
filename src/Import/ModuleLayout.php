<?php

namespace App\Import;

class ModuleLayout
{
    /**
     * @var array|string[]
     */
    private array $modulesArray = [
        '<h1>{{ title }}</h1>
         {{ paragraph|raw }}',
        '<h3>{{ subtitle }}</h3>
         {{ paragraph|raw }}',
        '{{ paragraphs|raw }}',
        '<div class="media">
                <img class="ml-3" src="{{ images|imageSrc(0)|imagine_filter(\'article_preview\') }}" alt="">
                <div class="media-body">
                    {{ paragraphs|raw }}
                </div>
            </div>',
        '<div class="media">
                <div class="media-body">
                    {{ paragraphs|raw }}
                </div>
                <img class="ml-3" src="{{ images|imageSrc(1)|imagine_filter(\'article_preview\') }}" alt="">
            </div>',
        '<div class="row">
                <div class="col-sm-6">
                    {{ paragraphs|raw }}
                </div>
                <div class="col-sm-6">
                    {{ paragraphs|raw }}
                </div>
            </div>',
        '<div class="media">
                <div class="media-body">
                    {{ paragraph|raw }}
                </div>
                <img class="ml-3" src="{{ images|imageSrc(2)|imagine_filter(\'article_preview\') }}" alt="">
            </div>',
    ];

    /**
     * @return array|array[]
     */
    public function getModuleLayout(): array
    {
        return $this->modulesArray;
    }
}