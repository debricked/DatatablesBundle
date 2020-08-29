<?php

namespace Sg\DatatablesBundle\Command;

use Sg\DatatablesBundle\Datatable\DatatableInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

class GenerateDatatableJavascriptsCommand extends Command
{

    use LockableTrait;

    protected static $defaultName = 'sg:datatables:generate:javascripts';
    private const ARGUMENT_OUTPUT = 'output';

    /**
     * @var DatatableInterface[]
     */
    private $datatables;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $projectDir;

    protected function configure()
    {
        $this
            ->setName(static::$defaultName)
            ->setDescription('Generates JavaScripts for datatables.')
            ->addArgument(
                static::ARGUMENT_OUTPUT,
                InputArgument::REQUIRED,
                'Output directory of generated javascripts'
            );
    }

    public function __construct(
        ?string $name = null,
        iterable $datatables,
        Environment $twig,
        string $projectDir
    ) {
        parent::__construct($name);
        $this->datatables = $datatables;
        $this->twig = $twig;
        $this->projectDir = $projectDir;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $filesystem = new Filesystem();

        foreach ($this->datatables as $datatable) {
            try {
                $datatableJavascript = $this->twig->render(
                    '@SgDatatables/datatable/datatable_js.js.twig',
                    [
                        'sg_datatables_view' => $datatable,
                    ]
                );
            } catch (\RuntimeException $e) {
                $output->writeln('<error>Failed to render datatable "'.$datatable->getName().'"</error>');

                return 1;
            }
            try {
                $filename = $this->projectDir.'/'.$input->getArgument(
                        static::ARGUMENT_OUTPUT
                    ).'/'.$datatable->getName().'.js';
                if ($filesystem->exists($filename) === true) {
                    $filesystem->remove($filename);
                }
                $filesystem->dumpFile(
                    $filename,
                    $datatableJavascript
                );
            } catch (IOException $exception) {
                $output->writeln(
                    '<error>Failed to save datatable"'.$datatable->getName().'"\'s javascript</error>'
                );

                return 2;
            }
        }

        $output->writeln(
            '<info>Successfully generated javascript files for '.\count($this->datatables)
            .' datatable(s)</info>'
        );

        $this->release();

        return 0;
    }

}