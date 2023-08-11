<?php

namespace App\Domain\Admin\Data;

enum AdminLogOperationEnum: string
{
    case REMOVE_ADMIN = 'remove admin';
    case ADD_ADMIN = 'add admin';
    case CHANGE_RANK = 'change admin rank';
    case ADD_RANK = 'add rank';
    case REMOVE_RANK = 'remove rank';
    case CHANGE_FLAGS = 'change rank flags';

    public function getCssClass(): string
    {
        return match($this) {
            AdminLogOperationEnum::ADD_RANK, AdminLogOperationEnum::CHANGE_FLAGS,
            AdminLogOperationEnum::REMOVE_RANK,
            AdminLogOperationEnum::CHANGE_RANK => 'info',
            AdminLogOperationEnum::REMOVE_ADMIN => 'danger',
            AdminLogOperationEnum::ADD_ADMIN => 'success'
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            AdminLogOperationEnum::REMOVE_ADMIN => "fa-solid fa-user-xmark",
            AdminLogOperationEnum::ADD_ADMIN => "fa-solid fa-user-plus",
            AdminLogOperationEnum::CHANGE_RANK => "fa-solid fa-user-pen",
            AdminLogOperationEnum::ADD_RANK => "fa-solid fa-id-card-clip",
            AdminLogOperationEnum::REMOVE_RANK => "fa-solid fa-rectangle-xmark",
            AdminLogOperationEnum::CHANGE_FLAGS => "fa-solid fa-flag",
        };
    }

    public function getShort(): string
    {
        return match($this) {
            AdminLogOperationEnum::REMOVE_ADMIN => $this->value,
            AdminLogOperationEnum::ADD_ADMIN => $this->value,
            AdminLogOperationEnum::CHANGE_RANK => "Change Rank",
            AdminLogOperationEnum::ADD_RANK => $this->value,
            AdminLogOperationEnum::REMOVE_RANK => $this->value,
            AdminLogOperationEnum::CHANGE_FLAGS => "Change Flags",
        };
    }
}
