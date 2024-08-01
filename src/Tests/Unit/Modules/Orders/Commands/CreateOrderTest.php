<?php

namespace Project\Tests\Unit\Modules\Orders\Commands;

use Project\Common\Product\Currency;
use Project\Modules\Shopping\Offers\Offer;
use Project\Common\Entity\Hydrator\Hydrator;
use Project\Modules\Shopping\Cart\Entity\Cart;
use Project\Modules\Shopping\Entity\Promocode;
use Project\Common\Services\Environment\Client;
use Project\Modules\Shopping\Order\Entity\Order;
use Project\Modules\Shopping\Offers\OfferBuilder;
use Project\Modules\Shopping\Order\Entity\OrderId;
use Project\Common\ApplicationMessages\Events\Event;
use Project\Modules\Shopping\Adapters\ClientsService;
use Project\Modules\Shopping\Discounts\DiscountsService;
use Project\Modules\Shopping\Api\DTO\Order\DeliveryInfo;
use Project\Tests\Unit\Modules\Helpers\ContactsGenerator;
use Project\Modules\Shopping\Api\Events\Orders\OrderCreated;
use Project\Modules\Shopping\Api\Events\Orders\OrderUpdated;
use Project\Common\Services\Environment\EnvironmentInterface;
use Project\Modules\Shopping\Order\Commands\CreateOrderCommand;
use Project\Common\ApplicationMessages\Buses\MessageBusInterface;
use Project\Modules\Shopping\Order\Entity\Delivery\DeliveryService;
use Project\Modules\Shopping\Discounts\Promocodes\Entity\PromocodeId;
use Project\Modules\Shopping\Cart\Repository\CartsRepositoryInterface;
use Project\Modules\Shopping\Order\Repository\OrdersRepositoryInterface;
use Project\Modules\Shopping\Order\Commands\Handlers\CreateOrderHandler;

class CreateOrderTest extends \PHPUnit\Framework\TestCase
{
    use ContactsGenerator;

    private readonly Hydrator $hydrator;

    private readonly OrdersRepositoryInterface $orders;
    private readonly CartsRepositoryInterface $carts;
    private readonly EnvironmentInterface $environment;
    private readonly ClientsService $clients;
    private readonly OfferBuilder $offerBuilder;
    private readonly DiscountsService $discountsService;
    private readonly MessageBusInterface $eventBus;

    private readonly OrderId $orderId;
    private readonly Client $client;
    private readonly Client $orderClient;
    private readonly Cart $cart;
    private readonly Offer $offer;
    private readonly Offer $offerWithNullId;
    private readonly Promocode $promo;
    private readonly Event $cartDeletedEvent;

    protected function setUp(): void
    {
        $this->hydrator = new Hydrator;

        $this->orders = $this->getMockBuilder(OrdersRepositoryInterface::class)->getMock();
        $this->carts = $this->getMockBuilder(CartsRepositoryInterface::class)->getMock();
        $this->environment = $this->getMockBuilder(EnvironmentInterface::class)->getMock();
        $this->clients = $this->getMockBuilder(ClientsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerBuilder = $this->getMockBuilder(OfferBuilder::class)->getMock();
        $this->discountsService = $this->getMockBuilder(DiscountsService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventBus = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $this->orderId = OrderId::random();
        $this->client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offer = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->offerWithNullId = $this->getMockBuilder(Offer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->promo = $this->getMockBuilder(Promocode::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartDeletedEvent = $this->getMockBuilder(Event::class)->getMock();
    }

    public function testCreate()
    {
        $this->environment->expects($this->once())
            ->method('getClient')
            ->willReturn($this->client);

        $this->carts->expects($this->once())
            ->method('getByClient')
            ->with($this->client)
            ->willReturn($this->cart);

        $this->cart->expects($this->once())
            ->method('getOffers')
            ->willReturn([$this->offer, $this->offer]);

        $this->offerBuilder->expects($this->exactly(2))
            ->method('from')
            ->with($this->offer)
            ->willReturnSelf();

        $this->offerBuilder->expects($this->exactly(2))
            ->method('withNullableId')
            ->willReturnSelf();

        $this->offerBuilder->expects($this->exactly(2))
            ->method('build')
            ->willReturn($this->offerWithNullId);

        $command = $this->getCommand();

        $this->clients->expects($this->once())
            ->method('findClient')
            ->with($command->firstName, $command->lastName, $command->phone, $command->email)
            ->willReturn($this->orderClient);

        $this->discountsService->expects($this->once())
            ->method('applyDiscounts')
            ->with([$this->offerWithNullId, $this->offerWithNullId])
            ->willReturn([$this->offerWithNullId, $this->offerWithNullId]);

        $this->cart->expects($this->once())
            ->method('getCurrency')
            ->willReturn(Currency::default());

        $this->cart->expects($this->once())
            ->method('getPromocode')
            ->willReturn($this->promo);

        $this->promo->expects($this->once())
            ->method('getId')
            ->willReturn(PromocodeId::random());

        $this->cart->expects($this->once())->method('delete');
        $this->carts->expects($this->once())->method('delete');

        $this->mockRepositoryAddMethod($command);

        $this->cart->expects($this->once())
            ->method('flushEvents')
            ->willReturn([$this->cartDeletedEvent]);

        $this->mockDispatcherCalls();

        $handler = new CreateOrderHandler(
            $this->orders,
            $this->carts,
            $this->environment,
            $this->clients,
            $this->discountsService,
            $this->offerBuilder,
        );

        $handler->setDispatcher($this->eventBus);
        $orderId = call_user_func($handler, $command);
        $this->assertSame($orderId, $this->orderId->getId());
    }

    private function getCommand(): CreateOrderCommand
    {
        return new CreateOrderCommand(
            firstName: uniqid(),
            lastName: uniqid(),
            phone: $this->generatePhone(),
            email: $this->generateEmail(),
            delivery: new DeliveryInfo(
                service: DeliveryService::NOVA_POST->value,
                country: uniqid(),
                city: uniqid(),
                street: uniqid(),
                houseNumber: uniqid(),
            ),
            customerComment: uniqid()
        );
    }

    private function mockRepositoryAddMethod(CreateOrderCommand $command)
    {
        $this->orders->expects($this->once())
            ->method('add')
            ->with($this->callback(function (Order $order) use ($command) {
                $this->assertSame($this->orderClient, $order->getClient()->getClient());
                $this->assertSame($command->firstName, $order->getClient()->getFirstName());
                $this->assertSame($command->lastName, $order->getClient()->getLastName());
                $this->assertSame($command->phone, $order->getClient()->getPhone());
                $this->assertSame($command->email, $order->getClient()->getEmail());

                $this->assertSame($command->delivery->service, $order->getDelivery()->getService()->value);
                $this->assertSame($command->delivery->country, $order->getDelivery()->getCountry());
                $this->assertSame($command->delivery->city, $order->getDelivery()->getCity());
                $this->assertSame($command->delivery->street, $order->getDelivery()->getStreet());
                $this->assertSame($command->delivery->houseNumber, $order->getDelivery()->getHouseNumber());

                $this->assertSame([$this->offerWithNullId, $this->offerWithNullId], $order->getOffers());
                $this->assertSame(Currency::default(), $order->getCurrency());
                $this->assertSame($this->promo, $order->getPromocode());
                $this->assertSame($command->customerComment, $order->getCustomerComment());

                $this->hydrator->hydrate($order, ['id' => $this->orderId]);
                return true;
            }));
    }

    private function mockDispatcherCalls()
    {
        $eventNum = 0;
        $this->eventBus->expects($this->exactly(3))
            ->method('dispatch')
            ->with($this->callback(function (Event $event) use (&$eventNum) {
                $eventsMap = [
                    $this->cartDeletedEvent::class,
                    OrderCreated::class,
                    OrderUpdated::class,
                ];

                $this->assertInstanceOf($eventsMap[$eventNum], $event);
                $eventNum++;
                return true;
            }));
    }
}