import React, { useState } from 'react'
import { gql, useQuery, useMutation } from '@apollo/client'
const OFFERS_QUERY = gql`
{
    allOffers{
        id,
        offer_name,
        options{
            id,
            name,
            factor,
            status,
            percentge_value,
        }
    }
}
`

const ALL_PRODUCTS = gql`
{
    allProducts{
        id,
        name
    }
}
`

const Edit_OPTION = gql`
    mutation($id:ID!, $factor:String!, $status:String!, $percentge_value:Float!){
        updateOfferOption(id:$id, factor:$factor, status:$status, percentge_value:$percentge_value){
            name,
            factor,
            status,
            percentge_value,
        }
    }
`


function Admin() {
    const offers = useQuery(OFFERS_QUERY);
    const products = useQuery(ALL_PRODUCTS);
    const [editOption, { data, error }] = useMutation(Edit_OPTION)

    console.log(products);
    const [form, setForm] = useState({
        id: '',
        name: '',
        factor: '',
        status: '',
        percentge_value: 0,
    })

    const handleSubmit = (e) => {
        e.preventDefault();
        editOption({
            variables: {
                id: form.id,
                factor: form.factor,
                status: form.status,
                percentge_value: +form.percentge_value
            },
            refetchQueries: [
                {
                    query: OFFERS_QUERY
                }
            ]
        })

    }

    return (
        <div className='container'>
            <div className='mb-2'>
                <h2>
                    All Offers
                </h2>
            </div>

            <table className="table">
                <thead>
                    <tr>
                        <th scope='col'>#</th>
                        <th scope='col' >Name</th>
                        <th scope='col-2'>Options</th>
                    </tr>
                </thead>
                <tbody>
                    {
                        offers.data && offers.data.allOffers.map((offer) => {
                            return (<tr key={offer.id}>
                                <th scope="row">{offer.id}</th>
                                <td>{offer.offer_name}</td>
                                <td>
                                    {offer.options.map((option) => {
                                        return <div key={option.id} className="d-flex justify-content-between mt-2">

                                            {option.percentge_value > 0 && <div className='col'>{option.percentge_value}%</div>}
                                            {!option.percentge_value && <div className='col'></div>}

                                            <div className='col px-2'>{option.name}</div>
                                            <div className='col px-2'>{option.factor}</div>
                                            <div className='col px-2'>
                                                <button
                                                    onClick={() => setForm({
                                                        id: option.id,
                                                        name: option.name,
                                                        factor: option.factor,
                                                        status: option.status,
                                                        percentge_value: option.percentge_value,
                                                    })}
                                                    type="button" className="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editOffer">
                                                    Edit
                                                </button>
                                            </div>
                                        </div>
                                    })}
                                </td>
                            </tr>)

                        })
                    }
                </tbody>
            </table>

            <div className="modal fade" id="editOffer" tabIndex="-1" aria-labelledby="editOfferLabel" aria-hidden="true">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h1 className="modal-title fs-5" id="editOfferLabel">Edit Offer Option</h1>
                            <button type="button" className="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form onSubmit={handleSubmit}>
                            <div className="modal-body">
                                <div className="mb-3">
                                    <label htmlFor="name" className="form-label">{form.name}</label>
                                    {
                                        !form.name === 'Product Id' &&
                                        <input value={form.factor}
                                            onChange={(e) => setForm({
                                                ...form,
                                                factor: e.target.value
                                            })} type="text" className="form-control" id="name" />
                                    }

                                    {
                                        form.name === 'Product Id' &&
                                        <select className="form-select form-select-lg mb-3 mt-2"
                                            value={form.factor}
                                            onChange={((e) => {
                                                setForm({
                                                    ...form,
                                                    factor: e.target.value
                                                })
                                            })}
                                        >
                                            {
                                                products.data &&
                                                products.data.allProducts.map((product) => {
                                                    return <option value={product.id}>
                                                        {product.name}
                                                    </option>
                                                })
                                            }
                                        </select>
                                    }

                                    {form.percentge_value > 0 &&
                                        <>
                                            <label htmlFor="name" className="form-label mt-2">Percntage</label>
                                            <input value={form.percentge_value}
                                                onChange={(e) => setForm({
                                                    ...form,
                                                    percentge_value: e.target.value
                                                })} type="text" className="form-control" id="name" />
                                        </>
                                    }

                                    <select value={form.status}
                                        onChange={(e) => setForm({
                                            ...form,
                                            status: e.target.value
                                        })}
                                        className="form-select mt-3" aria-label="Default select example"
                                    >
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" className="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    )
}

export default Admin