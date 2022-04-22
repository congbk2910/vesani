<?php
namespace Cminds\Salesrep\Model\Source;

class UsersConfigList
{
    protected $adminUsers;

    public function __construct(
        \Magento\User\Model\ResourceModel\User\Collection $adminUsers
    ) {
        $this->adminUsers = $adminUsers;
    }

    public function toOptionArray()
    {
        $collection   = $this->adminUsers->setOrder('firstname', 'asc')->load();

        $result   = [];
        $result[] = ['value' => "0", 'label' => __("No Salesrep")];

        foreach ($collection as $admin) {
            $result[] = [
              'value' => $admin->getId(),
              'label' => $admin->getFirstname()
                  .' '. $admin->getLastname() .' ('. $admin->getUsername() .')'];
        }

        return $result;
    }
}
