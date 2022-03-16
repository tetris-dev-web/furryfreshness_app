import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { WebhooksController } from './webhooks/webhooks.controller';
import { WebhookManageController } from './webhook_manage/webhook_manage.controller';
import { ConfigModule } from '@nestjs/config';
import Shopify, { ApiVersion } from '@shopify/shopify-api';

@Module({
  imports: [ConfigModule.forRoot()],
  controllers: [AppController, WebhooksController, WebhookManageController],
  providers: [AppService],
})

export class AppModule {
  onApplicationBootstrap() {
    Shopify.Context.initialize({
      API_KEY : process.env.API_KEY,
      API_SECRET_KEY : process.env.API_SECRET_KEY,
      SCOPES: ['read_orders', 'write_orders'],
      HOST_NAME: process.env.STORE_NAME,
      IS_EMBEDDED_APP: false,
      IS_PRIVATE_APP: true,
      API_VERSION: ApiVersion.October21 // all supported versions are available, as well as "unstable" and "unversioned"
    });
  }  
}
