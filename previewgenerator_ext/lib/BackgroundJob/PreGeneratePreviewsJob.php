<?php

declare(strict_types = 1);

namespace OCA\PreviewGeneratorExt\BackgroundJob;

use Exception;
use OCP\App\IAppManager;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;
use OCP\IConfig;
use OCP\Server;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class PreGeneratePreviews is a background job used to pre-generate previews
 *
 * @package OCA\PreviewGeneratorExt\BackgroundJob
 */
class PreGeneratePreviewsJob extends TimedJob {
    
    const INTERVAL = 60 * 10;
    
    private IAppManager $appManager;
    private IConfig $config;
    private LoggerInterface $loggerInterface;
    
    /**
     * @param IAppManager $appManager
     * @param IConfig $config
     * @param LoggerInterface $loggerInterface
     * @param ITimeFactory $timeFactory
     */
    public function __construct(
        IAppManager $appManager,
        IConfig $config,
        LoggerInterface $loggerInterface,
        ITimeFactory $timeFactory
    ) {
        parent::__construct($timeFactory);
        
        $this->setInterval(self::INTERVAL);
        $this->setTimeSensitivity(self::TIME_SENSITIVE);
        
        $this->appManager = $appManager;
        $this->config = $config;
        $this->loggerInterface = $loggerInterface;
    }
    
    /**
     * @param array $argument unused argument
     * @throws Exception
     */
    protected function run($argument): void {
        if ($this->config->getSystemValueBool('previewgenerator_ext_pre_generation_disabled', false)) {
            $this->loggerInterface->info('Job is disabled. Aborted.');
            return;
        }
        
        if (!$this->appManager->isInstalled('previewgenerator')) {
            $this->loggerInterface->info('Preview generator is missing or disabled. Aborted.');
            return;
        }
        
        $loggerOutput = new LoggerOutput($this->loggerInterface);
        
        $application = new Application();
        $application->add(Server::get('OCA\PreviewGenerator\Command\PreGenerate'));
        $application->setAutoExit(false);
        $result = $application->run(new ArrayInput(['command' => 'preview:pre-generate', '-v' => true]), $loggerOutput);
        
        if ($result !== 0) {
            return;
        }
        if ($loggerOutput->hasErrors()) {
            $this->loggerInterface->warning("Job completed with errors.");
            return;
        }
        $this->loggerInterface->info("Job completed.");
    }
    
}
