<?php

namespace Admingenerator\GeneratorBundle\Builder\Admin;

use Admingenerator\GeneratorBundle\Generator\Action;

/**
 * This builder generates php for custom actions
 * @author Piotr Gołębiewski <loostro@gmail.com>
 */
class ActionsBuilder extends BaseBuilder
{
    /**
     * @var array
     */
    protected $object_actions = array();

    /**
     * @var array
     */
    protected $batch_actions  = array();

    /**
     * (non-PHPdoc)
     * @see Admingenerator\GeneratorBundle\Builder.BaseBuilder::getYamlKey()
     */
    public function getYamlKey()
    {
        return 'actions';
    }

    /**
     * (non-PHPdoc)
     * @see \Admingenerator\GeneratorBundle\Builder\BaseBuilder::getVariables()
     */
    public function getVariables()
    {
        // If credentials are not globally defined,
        // check if an action have credentials
        if (null === $this->getVariable('credentials')) {
            $this->variables->set('credentials', false);
            foreach (array_merge($this->getObjectActions(), $this->getBatchActions()) as $action) {
                if ($action->getCredentials()) {
                    $this->variables->set('credentials', true);
                    break;
                }
            }
        }

        return parent::getVariables();
    }

    /**
     * Return a list of action from list.object_actions
     * @return array
     */
    public function getObjectActions()
    {
        if (0 === count($this->object_actions)) {
            $this->findObjectActions();
        }

        return $this->object_actions;
    }

    protected function setUserObjectActionConfiguration(Action $action)
    {
        if ($globalCredentials = $this->getGenerator()->getFromYaml('params.credentials')) {
            // If generator is globally protected by credentials
            // object actions are also protected
            $action->setCredentials($globalCredentials);
        }

        $builderOptions = $this->getVariable(
            sprintf('object_actions[%s]', $action->getName()),
            array(), true
        );

        $globalOptions = $this->getGenerator()->getFromYaml(
            'params.object_actions.'.$action->getName(), array()
        );

        if (null !== $builderOptions) {
            foreach ($builderOptions as $option => $value) {
                $action->setProperty($option, $value);
            }
        } elseif (null !== $globalOptions) {
            foreach ($globalOptions as $option => $value) {
                $action->setProperty($option, $value);
            }
        }
    }

    protected function addObjectAction(Action $action)
    {
        $this->object_actions[$action->getName()] = $action;
    }

    protected function findObjectActions()
    {
        $objectActions = $this->getVariable('object_actions', array());

        foreach ($objectActions as $actionName => $actionParams) {
            $action = $this->findObjectAction($actionName);
            if(!$action) {
                $action = new Action($actionName);
            }

            $this->setUserObjectActionConfiguration($action);
            $this->addObjectAction($action);
        }
    }



    /**
     * Return a list of batch action from list.batch_actions
     * @return array
     */
    public function getBatchActions()
    {
        if (0 === count($this->batch_actions)) {
            $this->findBatchActions();
        }

        return $this->batch_actions;
    }

    protected function setUserBatchActionConfiguration(Action $action)
    {
        if ($globalCredentials = $this->getGenerator()->getFromYaml('params.credentials')) {
            // If generator is globally protected by credentials
            // batch actions are also protected
            $action->setCredentials($globalCredentials);
        }

        $builderOptions = $this->getVariable(
            sprintf('batch_actions[%s]', $action->getName()),
            array(),
            true
        );

        $globalOptions = $this->getGenerator()->getFromYaml(
            'params.batch_actions.'.$action->getName(), array()
        );

        if (null !== $builderOptions) {
            foreach ($builderOptions as $option => $value) {
                $action->setProperty($option, $value);
            }
        } elseif (null !== $globalOptions) {
            foreach ($globalOptions as $option => $value) {
                $action->setProperty($option, $value);
            }
        }
    }

    protected function addBatchAction(Action $action)
    {
        $this->batch_actions[$action->getName()] = $action;
    }

    protected function findBatchActions()
    {
        $batchActions = $this->getVariable('batch_actions', array());

        foreach ($batchActions as $actionName => $actionParams) {
            $action = $this->findBatchAction($actionName);
            if(!$action) {
                $action = new Action($actionName);
            }

            $this->setUserBatchActionConfiguration($action);
            $this->addBatchAction($action);
        }
    }
}
