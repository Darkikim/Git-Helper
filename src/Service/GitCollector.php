<?php

namespace Darkikim\GitHelper\Service;

use Symfony\Component\HttpFoundation\File\Exception\NoFileException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GitCollector
{
    private string $projectDirectory;

    private string $gitRepositoryPath;

    public function __construct(string $projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
        $this->gitRepositoryPath = $projectDirectory.DIRECTORY_SEPARATOR.'.git';
    }

    /**
     * Gather data from .git directory
     */
    public function getGitData($fulldata = false): array
    {
        if(!is_dir($this->gitRepositoryPath)){
            throw new ResourceNotFoundException('No .git directory found under project directory');
        }

        $gitHEAD = file_get_contents($this->gitRepositoryPath.'/HEAD');

        $gitCurrentBranch = trim(preg_replace('/^.+refs\/heads\//', '', $gitHEAD));

        $gitFilename = $this->gitRepositoryPath.'/refs/heads/'.$gitCurrentBranch;
        if(!is_file($gitFilename)) {
            throw new NoFileException("Git /refs/heads/".$gitCurrentBranch." not found !");
        }

        $gitLastCommitHashFromBranch = trim(file_get_contents($gitFilename));
        if(!is_string($gitLastCommitHashFromBranch)) {
            throw new ResourceNotFoundException("No content extracted from /refs/heads/".$gitCurrentBranch);
        }

        $gitLastCommitDate = date('d-m-Y H:i:s', filemtime($gitFilename) );

        $process = new Process(['git', 'show', '--pretty=short', $gitLastCommitHashFromBranch], $this->projectDirectory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $gitLastCommitInformation = $process->getOutput();

        $gitLastCommitInformation = (preg_split('/\n|\r\n?/', $gitLastCommitInformation));
        $returnData = [
            'Current Branch Name'  => $gitCurrentBranch,
            'Last Commit Hash'     => $gitLastCommitHashFromBranch,
            'Author'               => ltrim($gitLastCommitInformation[1], 'Author:'),
            'Date'                 => $gitLastCommitDate,
            'Last Commit Message'  => trim($gitLastCommitInformation[3]),
        ];
        if(!$fulldata) {
            return $returnData;
        }

        $gitListOfChangedFiles = array_filter($gitLastCommitInformation, function ($var) {
            return (str_contains($var, 'diff --git a/'));
        });

        $gitListOfChangedFiles = preg_replace('/(?>diff\s--git\s\S+\s\S)/', '', $gitListOfChangedFiles);

        $returnData['List of Changed Files'] = $gitListOfChangedFiles;
        return $returnData;
    }
}