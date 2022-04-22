<?php
namespace Cminds\Salesrep\Model\Source;

class UsersList
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

        foreach ($collection as $admin) {
            $result[] = [
              'value' => $admin->getId(),
              'label' => $admin->getFirstname()
                  .' '. $admin->getLastname() .' ('. $admin->getUsername() .')'];
        }

        return $result;
    }
}
