<?php

namespace Darkikim\GitHelper\DataCollector;

use Darkikim\GitHelper\Service\GitCollector;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GitDataCollector extends AbstractDataCollector
{
    private GitCollector $collector;

    public function __construct(GitCollector $collector)
    {
        $this->collector = $collector;
    }

    public function getName(): string
    {
        return 'kikim.git_data_collector';
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $this->data = [
            "gitToolbarData"  => $this->collector->getGitData(),
            "gitProfilerData" => $this->collector->getGitData(true),
        ];
    }

    public function getGitToolbarData(): array
    {
        return $this->data['gitToolbarData'];
    }

    public function getGitProfilerData(): array
    {
        return $this->data['gitProfilerData'];
    }

    public static function getTemplate(): ?string
    {
        return '@GitHelper/data_collector/layout.html.twig';
    }
}