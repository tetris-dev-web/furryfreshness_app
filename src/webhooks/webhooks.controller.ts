import { Controller, Get } from '@nestjs/common';
import { AppService } from '../app.service';


@Controller('webhooks')
export class WebhooksController {
  constructor(private readonly appService: AppService) {}

  @Get('carts/update')
  cartsUpdate(): Array<string> {
    return ['a', 'b', 'c'];
  }
}
