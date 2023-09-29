<?php

namespace App\Service;

use App\Entity\Dto\PromotedWord;
use App\Entity\ValueObject\ArticleGenerateOptions;
use App\Entity\ValueObject\Range;
use App\Exception\DeserializationException;
use JsonSchema\Validator;
use Symfony\Component\HttpKernel\KernelInterface;

class ArticleOptionsDeserializer
{
    private string $schemasDir;

    /**
     * @param string          $resourcesDir
     * @param string          $schemasDir
     * @param KernelInterface $kernel
     */
    public function __construct(string $resourcesDir, string $schemasDir, KernelInterface $kernel)
    {
        $this->schemasDir = $kernel->getProjectDir() . '/' . $resourcesDir . $schemasDir;
    }

    /**
     * @param string $json
     *
     * @return ArticleGenerateOptions|array
     * @throws DeserializationException
     */
    public function deserializeJson(string $json)
    {
        $data = json_decode($json);
        $schema = file_get_contents($this->schemasDir . 'api_request_schema.json');
        $validator = new Validator();
        $validator->validate($data, json_decode($schema));

        if (!$validator->isValid()) {
            throw new DeserializationException('Unable to deserialize.', $validator->getErrors());
        }

        return (new ArticleGenerateOptions())
            ->setTheme($data->theme)
            ->setKeywords(array_values((array)$data->keyword))
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
