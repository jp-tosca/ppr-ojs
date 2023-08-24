<?php

import('tests.src.PPRTestCase');
import('tests.src.mocks.PPRPluginMock');
import('services.PPRWorkflowService');

class PPRWorkflowServiceTest extends PPRTestCase {

    const CONTEXT_ID = 130;
    private $defaultPPRPlugin;

    const WORKFLOW_SETTINGS = ['submissionCommentsForReviewerEnabled', 'submissionResearchTypeEnabled', 'displayContributorsEnabled', 'displaySuggestedReviewersEnabled'];

    public function setUp(): void {
        parent::setUp();
        $this->defaultPPRPlugin = new PPRPluginMock(self::CONTEXT_ID, []);
    }

    public function test_register_should_not_register_any_hooks_when_workflow_settings_are_false() {
        $publicSettings = [];
        foreach (self::WORKFLOW_SETTINGS as $workflowSetting) {
            $publicSettings[$workflowSetting] = false;
        }
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, $publicSettings, true);
        $target = new PPRWorkflowService($pprPluginMock);
        $target->register();

        $this->assertEquals(0, $this->countHooks());
    }

    public function test_register_should_register_commentsForReviewer_hooks_when_submissionCommentsForReviewerEnabled_is_true() {
        // DEFAULT VALUE IS NEEDED AS SOME SETTINGS ARE CONFIGURED TO BE TRUE BY DEFAULT IN THE PLUGIN SETTINGS
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionCommentsForReviewerEnabled' => true], false);
        $target = new PPRWorkflowService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Template::Workflow')));
    }

    public function test_register_should_register_researchType_hooks_when_submissionResearchTypeEnabled_is_true() {
        // DEFAULT VALUE IS NEEDED AS SOME SETTINGS ARE CONFIGURED TO BE TRUE BY DEFAULT IN THE PLUGIN SETTINGS
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['submissionResearchTypeEnabled' => true], false);
        $target = new PPRWorkflowService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Template::Workflow')));
    }

    public function test_register_should_register_displayContributors_hooks_when_displayContributorsEnabled_is_true() {
        // DEFAULT VALUE IS NEEDED AS SOME SETTINGS ARE CONFIGURED TO BE TRUE BY DEFAULT IN THE PLUGIN SETTINGS
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['displayContributorsEnabled' => true], false);
        $target = new PPRWorkflowService($pprPluginMock);
        $target->register();

        $this->assertEquals(2, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Template::Workflow')));
        $this->assertEquals(1, count($this->getHooks('LoadComponentHandler')));
    }

    public function test_register_should_register_suggestedReviewers_hooks_when_displaySuggestedReviewersEnabled_is_true() {
        // DEFAULT VALUE IS NEEDED AS SOME SETTINGS ARE CONFIGURED TO BE TRUE BY DEFAULT IN THE PLUGIN SETTINGS
        $pprPluginMock = new PPRPluginMock(self::CONTEXT_ID, ['displaySuggestedReviewersEnabled' => true], false);
        $target = new PPRWorkflowService($pprPluginMock);
        $target->register();

        $this->assertEquals(1, $this->countHooks());
        $this->assertEquals(1, count($this->getHooks('Template::Workflow')));
    }

    public function test_addPPRAuthorGridHandler_should_replace_the_component_name_when_it_matches_expected_value() {
        $target = new PPRWorkflowService($this->defaultPPRPlugin);
        $componentName = 'grid.users.author.AuthorGridHandler';
        $arguments = [& $componentName];
        $target->addPPRAuthorGridHandler('LoadComponentHandler', $arguments);

        $this->assertEquals('plugins.generic.pprOjsPlugin.services.PPRAuthorGridHandler', $componentName);
    }

    public function test_addPPRAuthorGridHandler_should_not_replace_the_component_name_when_it_does_not_match_expected_value() {
        $target = new PPRWorkflowService($this->defaultPPRPlugin);
        $componentName = 'component.name';
        $arguments = [& $componentName];
        $target->addPPRAuthorGridHandler('LoadComponentHandler', $arguments);

        $this->assertEquals('component.name', $componentName);
    }

    public function test_addCommentsForReviewerToWorkflow_should_append_template_content_to_output() {
        [$pprPluginMock, $pluginResourcePath] = $this->createPluginMock('ppr/workflowCommentsForReviewer.tpl');
        $output = 'current_output:';
        $smarty = $this->createSmartyMock($pluginResourcePath, 'template_contents');

        $arguments = [null, &$smarty, &$output];
        $target = new PPRWorkflowService($pprPluginMock);
        $target->addCommentsForReviewerToWorkflow('Template::Workflow', $arguments);

        $this->assertEquals('current_output:template_contents', $output);
    }

    public function test_addResearchTypeToWorkflow_should_append_template_content_to_output() {
        [$pprPluginMock, $pluginResourcePath] = $this->createPluginMock('ppr/workflowResearchType.tpl');
        $output = 'current_output:';
        $smarty = $this->createSmartyMock($pluginResourcePath, 'template_contents');

        $arguments = [null, &$smarty, &$output];
        $target = new PPRWorkflowService($pprPluginMock);
        $target->addResearchTypeToWorkflow('Template::Workflow', $arguments);

        $this->assertEquals('current_output:template_contents', $output);
    }

    public function test_addSuggestedReviewersToWorkflow_should_append_template_content_to_output() {
        [$pprPluginMock, $pluginResourcePath] = $this->createPluginMock('ppr/workflowSuggestedReviewers.tpl');
        $output = 'current_output:';
        $smarty = $this->createSmartyMock($pluginResourcePath, 'template_contents');

        $arguments = [null, &$smarty, &$output];
        $target = new PPRWorkflowService($pprPluginMock);
        $target->addSuggestedReviewersToWorkflow('Template::Workflow', $arguments);

        $this->assertEquals('current_output:template_contents', $output);
    }

    public function test_addContributorsToWorkflow_should_append_template_content_to_output() {
        [$pprPluginMock, $pluginResourcePath] = $this->createPluginMock('ppr/workflowContributors.tpl');
        $output = 'current_output:';
        $smarty = $this->createSmartyMock($pluginResourcePath, 'template_contents');

        $arguments = [null, &$smarty, &$output];
        $target = new PPRWorkflowService($pprPluginMock);
        $target->addContributorsToWorkflow('Template::Workflow', $arguments);

        $this->assertEquals('current_output:template_contents', $output);
    }

    private function createPluginMock($template) {
        $pluginResourcePath = "/plugin/template/path/" . $template;
        $pprPluginMock = $this->createMock(PeerPreReviewProgramPlugin::class);
        $pprPluginMock->expects($this->once())->method('getTemplateResource')->with($template)->willReturn($pluginResourcePath);
        return [$pprPluginMock, $pluginResourcePath];
    }

    private function createSmartyMock($fetchArgument, $fetchValue) {
        $smarty = $this->getMockBuilder(stdClass::class)->addMethods(['fetch'])->getMock();
        $smarty->expects($this->once())->method('fetch')->with($fetchArgument)->willReturn($fetchValue);
        return $smarty;
    }


}