import { Test, TestingModule } from '@nestjs/testing';
import { WebhookManageController } from './webhook_manage.controller';

describe('WebhookManageController', () => {
  let controller: WebhookManageController;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [WebhookManageController],
    }).compile();

    controller = module.get<WebhookManageController>(WebhookManageController);
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });
});
