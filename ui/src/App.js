import { 
  HydraAdmin,
  ResourceGuesser,
  CreateGuesser,
  InputGuesser
} from "@api-platform/admin";

import { DateTimeInput } from 'react-admin';

import Select from 'react-select'
import Creatable, { useCreatable } from 'react-select/creatable';


const options = [
  { value: 'chocolate', label: 'Chocolate' },
  { value: 'strawberry', label: 'Strawberry' },
  { value: 'vanilla', label: 'Vanilla' }
]

const OperationsCreate = props => (
  <CreateGuesser {...props}>
    <InputGuesser source="name" />
    <DateTimeInput source="startedAt" />
    <DateTimeInput source="endedAt" />
    <Select options={options} source="tags" />
  </CreateGuesser>
);


// Replace with your own API entrypoint
// For instance if https://example.com/api/books is the path to the collection of book resources, then the entrypoint is https://example.com/api
// export default () => (

//   <HydraAdmin entrypoint="%API_URL%">
//     <ResourceGuesser name="tags" />
//     <ResourceGuesser name="operations" create={OperationsCreate} />
//   </HydraAdmin>
// );

export default () => (
  <HydraAdmin entrypoint={process.env.REACT_APP_API_URL}>
    <ResourceGuesser name="operations" create={OperationsCreate} />
    <ResourceGuesser name="tags" />
  </HydraAdmin>
);
