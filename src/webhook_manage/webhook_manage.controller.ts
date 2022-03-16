import { Controller, Get } from '@nestjs/common';
import { AppService } from 'src/app.service';
import Shopify, {DataType} from '@shopify/shopify-api';

@Controller('webhook_manage')
export class WebhookManageController {
  constructor(private readonly appService: AppService) {}

  @Get('list')
  async getList(): Promise<any> {
    const client = new Shopify.Clients.Rest(process.env.STORE_NAME, process.env.ACCESS_TOKEN);
    const data = await client.get({
      path: 'webhooks',
    });
    return data;
  }

  @Get('add')
  async addCartChange(): Promise<any> {
    const client = new Shopify.Clients.Rest(process.env.STORE_NAME, process.env.ACCESS_TOKEN);
    const data = await client.post({
      path: 'webhooks',
      data: {"webhook":{"topic":"orders\/create","address":"https:\/\/example.hostname.com\/","format":"json","fields":["id","note"]}},
      type: DataType.JSON,
    });
    return data;
  }

  @Get('delete')
  async deleteCartChange(): Promise<any> {
    const client = new Shopify.Clients.Rest(process.env.STORE_NAME, process.env.ACCESS_TOKEN);
    const data = await client.delete({
      path: 'webhooks/1055271911563',
    });
    return data;
  }

  @Get('graphql')
  async testGraphQL(): Promise<any> {
    const client = new Shopify.Clients.Storefront(
      process.env.STORE_NAME,
      process.env.ACCESS_TOKEN,
    );
    // Use client.query and pass your query as `data`
    const products = await client.query({
      data: `{
        products (first: 10) {
          edges {
            node {
              id
              title
              variants (first: 10 ) {
                edges {
                  node {
                    id
                    title
                  }
                }
              }
            }
          }
        }
      }`,
    });
        
    return products;
  }
}
