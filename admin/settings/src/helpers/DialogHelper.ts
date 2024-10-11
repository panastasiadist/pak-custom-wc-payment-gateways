import DialogResult from '../enums';
import { watch } from 'vue';

export default class DialogHelper<I, O> {
  public isOpen = false;
  private input?: I;
  private output?: O;
  public result: DialogResult = DialogResult.Ok;

  close(result: DialogResult, output?: O) {
    this.isOpen = false;
    this.result = result;
    this.output = output;
  }

  getInput(): I | undefined {
    return this.input;
  }

  getOutput(): O | undefined {
    return this.output;
  }

  getResult(): DialogResult {
    return this.result;
  }

  async show(input: I) {
    this.isOpen = true;
    this.input = input;

    return new Promise<this>((resolve) => {
      watch(
        () => this.isOpen,
        (isOpen) => isOpen || resolve(this),
      );
    });
  }
}
