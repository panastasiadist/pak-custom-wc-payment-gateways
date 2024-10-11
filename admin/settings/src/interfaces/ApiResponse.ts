export default interface ApiResponse<T> {
  result: 'success' | 'failure';
  data: T;
}
