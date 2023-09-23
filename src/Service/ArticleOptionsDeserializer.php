<?php

namespace App\Service;

use App\Entity\Dto\PromotedWord;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use JsonSchema\Validator;

class ArticleOptionsDeserializer
{
    /**
     * @param string $json
     *
     * @return ArticleGenerateOptions|array
     */
    public function deserializeJson(string $json)
    {
        $data = json_decode($json);
        $schema = <<<'JSON'
{
  "type": "object",
  "properties": {
    "theme": { "type": "string" },
    "keywords": {
      "properties": {
        "0": { "type": "string" },
        "1": { "type": "string" },
        "2": { "type": "string" },
        "3": { "type": "string" },
        "4": { "type": "string" },
        "5": { "type": "string" },
        "6": { "type": "string" }
      },
      "additionalProperties": false,
      "required": ["0"]
    },
    "title": {
      "type": "string",
      "maxLength": 255 
    },
    "words": {
      "type": "array",
      "items": {
        "type": "object",
        "properties": {
          "word": { "type": "string" },
          "count": {
            "type": "integer",
            "minimum": 0,
            "exclusiveMinimum": true
          }
        },
        "required": ["word", "count"]
      },
      "minItems": 1
    },
    "images": {
      "type": "array",
      "items": {
        "type": "string",
        "format": "uri"
      }
    }
  },
  "anyOf": [
    {
      "properties": {
        "size": {
          "type": "integer",
          "minimum": 0,
          "exclusiveMinimum": true
        }
      }
    },
    {
      "properties": {
        "size": {
          "type": "array",
          "items": {
            "type": "integer",
            "minimum": 0,
            "exclusiveMinimum": true
          },
          "maxItems": 2,
          "minItems": 2 
        }
      }
    }
  ],
  "required": ["theme", "keywords", "size", "words"]
}
JSON;
        $validator = new Validator();
        $validator->validate($data, json_decode($schema));

        if (!$validator->isValid()) {
            return $validator->getErrors();
        }
        dump($data);

        return (new ArticleGenerateOptions())
            ->setTheme($data->theme)
            ->setKeywords(array_values((array)$data->keywords))
            ->setTitle($data->title ?? null)
            ->setSize(Range::from($data->size))
            ->setPromotedWords(
                array_map(function ($word) {
                    return PromotedWord::create($word->word, $word->count);
                }, $data->words)
            )
            ->setImages($data->images ?? [])
        ;
    }
}
