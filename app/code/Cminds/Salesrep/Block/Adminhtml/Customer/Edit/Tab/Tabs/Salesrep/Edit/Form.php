<?php
namespace Cminds\Salesrep\Block\Adminhtml\Customer\Edit\Tab\Tabs\Salesrep\Edit;

use Cminds\Salesrep\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\User\Model\ResourceModel\User\Collection;

class Form extends Generic
{
    /**
     * Core Registry.
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Sales Repository Helper.
     *
     * @var Data
     */
    protected $salesrepHelper;

    /**
     * Admin Users.
     *
     * @var Collection
     */
    protected $adminUsers;

    /**
     * Admin Session.
     *
     * @var Session
     */
    protected $adminSession;

    /**
     * Customer Repository.
     *
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Data $salesrepHelper
     * @param Collection $adminUsers
     * @param Session $adminSession
     * @param CustomerRepositoryInterface $customer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $salesrepHelper,
        Collection $adminUsers,
        Session $adminSession,
        CustomerRepositoryInterface $customer
    ) {
        parent::__construct($context, $registry, $formFactory);

        $this->registry = $registry;
        $this->salesrepHelper = $salesrepHelper;
        $this->adminUsers = $adminUsers;
        $this->adminSession = $adminSession;
        $this->customerRepositoryInterface = $customer;
    }

    /**
     * Prepare form.
     *
     * @return Form
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'method' => 'post'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'customer_sales_representative',
            ['legend' => __('Assign a Sales Rep')]
        );

        $assignOrChangeRep = $this->_authorization->isAllowed(
            'Cminds_Salesrep::assign_or_change_assigned_sales_rep'
        );

        if ($assignOrChangeRep) {
            $fieldset->addField(
                'salesrep_rep_id',
                'select',
                [
                    'name' => 'salesrep_rep_id',
                    'label' => __('Representative'),
                    'id' => 'salesrep_rep_id',
                    'title' => __('Representative'),
                    'required' => false,
                    'values' => $this->salesrepHelper->getAdmins(),
                    'value' => $this->getSalesrepId(),
                    'data-form-part' => 'customer_form',
                    'note' => __('The sales rep you assign above will be automatically credited with all of this customer\'s future orders. If you change the sales rep, only future orders will be credited to the newly assigned rep.')
                ]
            );
        } else {
            $fieldset->addField(
                'salesrep_rep_id',
                'note',
                [
                    'name' => 'salesrep_rep_id',
                    'label' => __('Representative'),
                    'id' => 'salesrep_rep_id',
                    'title' => __('Representative'),
                    'required' => false,
                    'text' => $this->getAdminName($this->getSalesrepId()),
                    'data-form-part' => 'customer_form',
                ]
            );
        }

        return parent::_prepareForm();
    }

    /**
     * Get sales rep id.
     *
     * @return mixed|string
     */
    public function getSalesrepId()
    {
        $customerId = $this->_request->getParam('id');

        if ($customerId) {
            $customerObject = $this->
            customerRepositoryInterface->getById($customerId);

            $salesrepData = $customerObject
                ->getCustomAttribute('salesrep_rep_id');
            if ($salesrepData) {
                if ($salesrepData->getValue() != null) {
                    return (int)$salesrepData->getValue();
                }
            }
        }

        return '';
    }

    /**
     * Get admin name.
     *
     * @param $id
     *
     * @return Phrase
     */
    public function getAdminName($id)
    {
        $adminArray = $this->salesrepHelper->getAdmins();

        if (isset($id) && !empty($id)) {
            foreach ($adminArray as $admin) {
                if ($id == $admin['value']) {
                    return $adminArray['label'];
                }
            }
        } else {
            return __('No Sales Rep');
        }
    }
}
