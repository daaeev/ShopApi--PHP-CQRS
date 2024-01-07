<?php

namespace App\Http\Controllers\Api\Promotions;

use App\Http\Controllers\BaseApiController;

class PromotionsController extends BaseApiController
{
    public function create(Requests\CreatePromotionRequest $request)
    {
        $id = $this->dispatchCommand($request->getCommand());
        return $this->success(['id' => $id], 'Promotion created');
    }

    public function update(Requests\UpdatePromotionRequest $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(
            ['id' => (int) $request->get('id')],
            'Promotion updated'
        );
    }

    public function delete(Requests\DeletePromotionRequest $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(
            ['id' => (int) $request->get('id')],
            'Promotion deleted'
        );
    }

    public function disable(Requests\DisablePromotionRequest $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(
            ['id' => (int) $request->get('id')],
            'Promotion disabled'
        );
    }

    public function enable(Requests\EnablePromotionRequest $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(
            ['id' => (int) $request->get('id')],
            'Promotion enabled'
        );
    }

    public function addDiscount(Requests\AddPromotionDiscountRequest $request)
    {
        $this->dispatchCommand($request->getCommand());
        return $this->success(
            ['id' => (int) $request->get('id')],
            'Promotion discount added'
        );
    }

    public function removeDiscount(Requests\RemovePromotionDiscountRequest $request)
    {
        $this->dispatchCommand($request->getCommand());
        $output = [
            'promotionId' => (int) $request->get('id'),
            'discountId' => (int) $request->get('discountId'),
        ];

        return $this->success($output, 'Promotion discount removed');
    }
}