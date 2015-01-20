<?php

namespace Lollypop\GearBundle\Deployer;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhpSecLibDeployer
 *
 * @author seshachalam
 */
use Plum\Deployer\DeployerInterface;
use Plum\Server\ServerInterface;
use Plum\Exception\SshException;
use \Net_SSH2;

class PhpSecLibDeployer implements DeployerInterface
{

    /**
     * The SSH connection
     *
     * @var ressource
     */
    protected $con;

    /**
     * {@inheritDoc}
     */
    public function doDeploy(ServerInterface $server, array $options, $dryRun)
    {
        $commands = isset($options['commands']) ? $options['commands'] : array();
        if(0 === count($commands)) {
            // The SSH deployer is useless if the user has no command
            return;
        }

        if(null === $server->getPassword()) {
            throw new SshException('No password found for the server.');
        }

        $this->connect($server);

        if(false === $dryRun) {
            foreach($commands as $command) {
                // We need to jump to the right directory..
                $command = sprintf('cd %s && %s', $server->getDir(), $command);
                $this->exec($command);
            }
        }

        $this->disconnect();
    }

    /**
     * Open the SSH connection
     *
     * @param Plum\Server\ServerInterface $server
     */
    protected function connect(ServerInterface $server)
    {
        $ssh = new Net_SSH2($server->getHost(), $server->getPort());
        if(!$ssh->login($server->getUser(), $server->getPassword())) {
            throw new SshException(sprintf('Authorization failed for user "%s"',
                                           $server->getUser()));
        }
        $this->con = $ssh;
    }

    /**
     * Close the SSH connection
     */
    protected function disconnect()
    {
        $this->exec('echo "EXITING" && exit;');
    }

    /**
     * Execute a SSH command
     *
     * @param string $cmd The SSH command
     *
     * @return string The output
     */
    protected function exec($cmd)
    {
        return $this->con->exec($cmd);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'php-ssh';
    }

    /**
     * {@inheritDoc}
     */
    public function deploy(ServerInterface $server, array $options = array())
    {
        $options = array_merge($options, $server->getOptions());

        $dryRun = false;
        if (isset($options['dry_run']) && $options['dry_run']) {
            $dryRun = true;
        }

        return $this->doDeploy($server, $options, $dryRun);
    }

}
