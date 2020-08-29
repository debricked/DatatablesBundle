<?php
/**
 * Created by PhpStorm.
 * User: oscar
 * Date: 2018-06-20
 * Time: 16:20
 */

namespace Sg\DatatablesBundle\Tests\Command;

use Sg\DatatablesBundle\Command\GenerateDatatableJavascriptsCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateDatatableJavascriptsCommandTest extends KernelTestCase
{

    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $application->add($kernel->getContainer()->get(GenerateDatatableJavascriptsCommand::class));

        $command = $application->find(GenerateDatatableJavascriptsCommand::getDefaultName());
        $commandTester = new CommandTester($command);
        $outputDir = 'Tests/assets/js/Datatable';
        $commandTester->execute(
            [
                'command' => $command->getName(),
                'output' => $outputDir,
            ]
        );

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Successfully generated javascript files for 1 datatable(s)', $output);
        $postDatatableFileUri = "{$kernel->getProjectDir()}/{$outputDir}/post_datatable.js";
        $this->assertFileExists($postDatatableFileUri);
        $postDatatableFileData = \file_get_contents($postDatatableFileUri);
        $this->assertStringContainsString('"#post_datatable-language"', $postDatatableFileData);
    }

}